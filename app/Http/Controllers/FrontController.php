<?php
namespace burnvideo\Http\Controllers;
//opcache_reset();

use burnvideo\Events\CreatedOrderEvent;
use Sentinel;
use DB;
use Braintree_Configuration;
use Braintree_ClientToken;
use Braintree_Customer;
use Braintree_Transaction;
use Braintree_PaymentMethod;
use Input;
use Validator;
use Mailgun;
use Redirect;
use Session;
use Response;
use Mail;
use Log;

use burnvideo\Models\PromoCode;
use burnvideo\Models\PromoCodeUse;
use burnvideo\Models\User;
use burnvideo\Models\FileModel;
use burnvideo\Models\Order;
use burnvideo\Models\OrderFile;
use burnvideo\Models\OrderShipping;
use burnvideo\Models\Transaction;
use burnvideo\Models\PaymentMethodUpdate;
use burnvideo\Models\Category;
use burnvideo\Models\Post;
use burnvideo\Models\MessageModel;
use burnvideo\Models\MessageUserModel;
use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\Filesystem;
use Storage;

class FrontController extends BaseController {

    private $states = [
        'AL', 'AK', 'AS', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DC', 'DE', 'FL', 'GA', 'GU', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC',
        'ND', 'OH', 'OK', 'OR', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UM', 'UT', 'VT', 'VA', 'VI', 'WA', 'WV', 'WI', 'WY'
    ];

    private function initBrainTree() {
        Braintree_Configuration::environment(config('services.braintree.mode'));
        Braintree_Configuration::merchantId(config('services.braintree.merchant_id'));
        Braintree_Configuration::publicKey(config('services.braintree.public_key'));
        Braintree_Configuration::privateKey(config('services.braintree.private_key'));
    }

    private function getMediaType($filekey) {
		$tmp = explode('.', $filekey);
		$fileExt = strtolower(end($tmp));
		switch ($fileExt) {
		case 'm4v':
        case 'f4v':    
		case 'mov':
		case 'avi':
		case 'mpg':
		case 'mpeg':
		case 'mp4':
		case 'wmv':
		case '3gp':
		case 'mts':
		case '3g2':
			// etc
			return "video";
		}
		return "image";
    }

    public function getOrderHistory() {
        $user = Sentinel::getUser();

        $orders = DB::select('select o.*, u.first_name, u.last_name from orders o '
                        . ' left join users u on u.id = o.uid where o.uid = ' . $user->id . ' order by o.id asc ');

        return view('front.order-history')
                        ->with('user', $user)
                        ->with('orders', $orders)
                        ->with('states', $this->states)
                        ->with('mon_nextday', !empty($user->mon_nextday) ? $user->mon_nextday : '0');
    }

    public function getIndex() {
        $user = Sentinel::getUser();
        if (empty($user)) {
            return view('landing.index')
                            ->with('user', $user);
        } else {
            return view('front.index')
                            ->with('user', $user);
        }
    }

    public function getRegisterCard() {
        $user = Sentinel::getUser();

        try {  
            self::initBrainTree();
  
            $clientToken = Braintree_ClientToken::generate();

            return view('front.register-card')
                            ->with('user', $user)
                            ->with('clientToken', $clientToken);
        } catch (Exception $e) {
            return view('front.register-card')
                            ->with('user', $user)
                            ->withErrors(array('message' => $e->getMessage()));
        }
    }

    function postRegisterCard() {
        $user = Sentinel::getUser();

        try {
            self::initBrainTree();

            $nonce = Input::get('payment_method_nonce');

            if ($user->customer_id) {
                $customer = Braintree_Customer::find($user->customer_id);

				if(isset($customer->creditCards[0]->token)){
					$token = $customer->creditCards[0]->token;
	                Braintree_PaymentMethod::delete($token);
				}
                
                $result = Braintree_PaymentMethod::create([
                            'customerId' => $user->customer_id,
                            'paymentMethodNonce' => $nonce,
							'options' => [
								'makeDefault' => true,
								'failOnDuplicatePaymentMethod' => true
							]
                ]);

				$paymentmethodupdate = new PaymentMethodUpdate;
				$paymentmethodupdate->uid = $user->id;
				$paymentmethodupdate->paymentmethodnonce = $nonce;
				$paymentmethodupdate->result = $result;
				$paymentmethodupdate->customer = $customer;
				$paymentmethodupdate->type = 0;
				$paymentmethodupdate->save();	

                if ($result->success) {
					if ($result->paymentMethod->isDefault()) {
						return Redirect::to('order')
                                    ->with('user', $user)
                                    ->with(array('success' => 'Payment method change success'));
					}
					else{
						return Redirect::to('order')
                                    ->with('user', $user)
                                    ->with(array('success' => 'Payment method has not changed.'));
					}
                }else {
  					$err_msg = '';                  
                    foreach ($result->errors->deepAll() AS $error) {
                        $err_msg .= $error->code . ": " . $error->message . "\n";
                    }
					if(empty($err_msg))
						$err_msg = $result->message;
                    return Redirect::to('register-card')
                                    ->with('user', $user)
                                    ->withErrors(array('message' => $err_msg));
                }
            } else {
                $result = Braintree_Customer::create([
                            'firstName' => $user->first_name,
                            'email' => $user->email,
                            'paymentMethodNonce' => $nonce
                ]);

                if ($result->success) {
                    $customer_id = $result->customer->id;
                    $user->customer_id = $customer_id;
                    $user->save();

                    return Redirect::to('order')
                                    ->with('user', $user)
                                    ->with(array('success' => 'Payment Register success'));
                } else {
                    $err_msg = '';
                    foreach ($result->errors->deepAll() AS $error) {
                        $err_msg .= $error->code . ": " . $error->message . "\n";
                    }
					if(empty($err_msg))
						$err_msg = $result->message;

                    return Redirect::to('register-card')
                                    ->with('user', $user)
                                    ->withErrors(array('message' => $err_msg));
                }
            }
		
        } catch (Exception $e) {
            return Redirect::to('register-card')
                            ->with('user', $user)
                            ->withErrors(array('message' => $e->getMessage()));
        }
    }

    function ajaxRegisterCard() {
        $user = Sentinel::getUser();

        try {
            self::initBrainTree();

            $nonce = Input::get('payment_method_nonce');

            if ($user->customer_id) {
                $customer = Braintree_Customer::find($user->customer_id);

				if(isset($customer->creditCards[0]->token)){
					$token = $customer->creditCards[0]->token;
	                Braintree_PaymentMethod::delete($token);
				}

                //$token = $customer->creditCards[0]->token;

                Braintree_PaymentMethod::delete($token);

                $result = Braintree_PaymentMethod::create([
                            'customerId' => $user->customer_id,
                            'paymentMethodNonce' => $nonce
                ]);

                if ($result->success) {

                    return Response::json(['retcode' => 200]);
                } else {
                    $err_msg = '';
                    foreach ($result->errors->deepAll() AS $error) {
                        $err_msg .= $error->code . ": " . $error->message . "\n";
                    }
					if(empty($err_msg))
						$err_msg = $result->message;

                    return Response::json(['retcode' => 201, 'msg' => $err_msg]);
                }
            } else {
                $result = Braintree_Customer::create([
                            'firstName' => $user->first_name,
                            'email' => $user->email,
                            'paymentMethodNonce' => $nonce
                ]);

                if ($result->success) {
                    $customer_id = $result->customer->id;
                    $user->customer_id = $customer_id;
                    $user->save();

                    return Response::json(['retcode' => 200]);
                } else {
                    $err_msg = '';
                    foreach ($result->errors->deepAll() AS $error) {
                        $err_msg .= $error->code . ": " . $error->message . "\n";
                    }

					if(empty($err_msg))
						$err_msg = $result->message;
                    return Response::json(['retcode' => 201, 'msg' => $err_msg]);
                }
            }
        } catch (Exception $e) {
            return Redirect::to('register-card')
                            ->with('user', $user)
                            ->withErrors(array('message' => $e->getMessage()));
        }
    }

    public function postUploadFileToS3(Request $request)
    {
        
        @ini_set("memory_limit", "-1");
        @set_time_limit(0);
        @ini_set('post_max_size', '4096M');
        @ini_set('upload_max_filesize', '4096M');


        $uploadFileName = $request->input('uploadKey');
        
        try {
            
            $uploadFile = $request->file('file');
            $disk = Storage::disk('s3');
            
            //$t = $disk->put($uploadFileName, file_get_contents($uploadFile), 'public');
            
            $t = $disk->put($uploadFileName, fopen($uploadFile, 'r+'));

            
            $s3FileUrl = Storage::disk('s3')->url($uploadFileName);


            
            return Response::json([
                'retcode' => 200, 
                'furl' => $s3FileUrl,
                'fname' => $uploadFileName
            ]);
            
        } catch  (Exception $e) {
            return Response::json(['retcode' => 201]);
        }

        
    }

    public function postFileUpload() {
        $user = Sentinel::getUser();

        try {
            $arr = array();
            $file = Input::file('box');
            //$arr = Session::get("filenames");
            //$flnm = $file->getClientOriginalName();
            //if(in_array($flnm,$arr))
            //{
            //return;	
            //}
            //else
            //{ 
            //$arr[] = $flnm;
            //	Session::put("filenames",$arr);
            //} 

            $orderid = Input::get('orderid');
            $stid = DB::table('orders')
                    ->where('id', $orderid)
                    ->pluck("status");

            if ($stid == 2) {
                Session::forget("orderid");
            }
            if (!Session::has('orderid')) {
                $o1 = new Order;
                $o1->uid = $user->id;
                $o1->save();
                $orderid = $o1->id;
                Session::put('orderid', $orderid);
            } else {
                $orderid = Session::get('orderid');
            }

            if ($file->isValid()) {

                $mime = $file->getMimeType();

                //$destinationPath = '/var/www/html/public/uploads/user/user_' . $user->id;
                $destinationPath = 'uploads/order/' . $orderid;
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath);
                }
                $filename = date('Ymd-His') . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $filename);

                // insert into file table
                $f = new FileModel;
                $f->uid = $user->id;
                $f->ftype = explode('/', $mime)[0];
                $f->furl = $destinationPath . '/' . $filename;
                $f->ftsurl = $filename;
                $f->fzipurl = '';
                $f->fplaytime = 0;
                $f->fweight = 1;
                $f->finserttime = time();
                $f->save();

                // insert into order_file table

                $cnt = DB::table('order_file')
                        ->select(DB::raw('count(*) as cnt'))
                        ->where('oid', $orderid)
                        ->where('fid', $f->id)
                        ->pluck("cnt");
                if ($cnt == 0) {
                    $order_file = new OrderFile;
                    $order_file->oid = $orderid;
                    $order_file->fid = $f->id;
                    $order_file->save();
                }
                return Response::json(['retcode' => 200, 'fid' => $f->id]);
            }
        } catch (Exception $e) {
            return Response::json(['retcode' => 201]);
        }
    }

    public function ajaxPay() {
        $input = Input::all();
        
        
		// Log::info(print_r(Input::all(), true));

		$paramuid = Input::get('uid');
        $user = Sentinel::getUser();
        if ( empty($user) || !$user ){
            $user = Sentinel::findById($paramuid);
        }

        $file_ids = array();

        $file_count = Input::get('file_counter');

        if (empty($user->customer_id)) {
            
            if (isset($paramuid) && !empty($paramuid)){
                $user = User::find($paramuid);

                if (empty($user->customer_id)) {
                    return Response::json(['retcode' => 202, 'msg' => 'Payment method not registered']);
                }
            } else {
                return Response::json(['retcode' => 202, 'msg' => 'Payment method not registered']);
            }


        }

        $input = Input::all();
        $self_order = Input::get('self_order');
        $additional_order = Input::get('additional_order', []);
        $dvdtitle = Input::get('dvd_title');
        $dvdcaption = '';
        $mediaBoxcount = Input::get('filled_mediabox_cnt');
        $shipping = $additional_order;
        $shipping[] = $self_order;
        $preorderid = Input::get('preorderid');

        $cnt_order_dvds = $self_order['count'] * 1;

        foreach ($additional_order as $item) {
            $cnt_order_dvds += $item['count'];
        }

//        print_r($cnt_order_dvds);
//        exit;


//      if ($preorderid == 0){//new order
//
//          $o1 = new Order;
//          $o1->uid = $user->id;
//            $time = date('YmdHis', time());
//            $format = "OU%'.05d-%s";
//            $orderTag = sprintf($format, $user->id, $time);
//            $o1->orderTag = $orderTag;            
//            $o1->devicetype = 0;            
//            $o1->status = "";            
//            $o1->weight = "";     
//            $o1->inserttime = time();
//            $o1->updatetime = time();
//          $o1->save();
//          $orderid = $o1->id;
//          Session::put('orderid', $orderid);
//
//      }

        try {

            self::initBrainTree();


            // monthly check
            $transactions = DB::select("select * from `transaction` t where uid = " . $user->id
                            . " order by t.paytime desc limit 1");
        
            $pay_month = 0;
            if (empty($transactions)) {
                $pay_month = config('services.price-per-monthly');

                $user->mon_nextday = strtotime('+1 month', time());
                $user->mon_weight = 40;
                $user->mon_freedvd = 1;
                $user->save();
            }

            if ($user->first_ordertime == null || $user->first_ordertime == 0) {
                $user->first_ordertime = time();
                $user->save();
            }

            $paydvdcount = $cnt_order_dvds;
            if ($user->mon_freedvd == 1) {
                $user->mon_freedvd = 0;
                $user->save();
                $paydvdcount = $paydvdcount - 1;
            }

            $pay_price = config('services.price-per-dvd') * intval($paydvdcount) + $pay_month;

            $deductVal = 0;
            if (array_key_exists('promocode', $input) && $input['promocode']) {
                $promo = PromoCode::where('name', $input['promocode'])->first()->toArray();
                if ($promo['type'] == 'value') {
                    $deductVal = $promo['value'];
                } else {
                    $deductVal = ($pay_price * $promo['value']) / 100;
                }
            } else {
                $promo['id'] = 0;
            }

            $final_price = $pay_price - $deductVal;
            $final_price = number_format($final_price, 2);

            if ($final_price >= 0) {
                $result = Braintree_Transaction::sale(
                    [
                        'customerId' => $user->customer_id,
                        'amount' => $final_price
                    ]
                );

                if ($result->success) {
                    // create order

                    $uid = $user->id;
                    $slotcount = $mediaBoxcount;

                    // --------- create order -- begin --
                    $time = date('YmdHis', time());
                    $format = "OU%'.05d-%s";
                    $orderTag = sprintf($format, $uid, $time);
                    $result = 0;
                    
                    if ($preorderid == 0) {

//                        if (!Session::has('orderid')) {
//                            $order = new Order;
//                        } else {
//                            $order = Order::find(Session::get('orderid'));
//                        }
                        $order = new Order;

                        $order->uid = $uid;
                        $order->orderTag = $orderTag;
                        $order->weight = $slotcount;
                        $order->devicetype = 0; //$devicetype;
                        $order->dvdtitle = $dvdtitle;
                        $order->dvdcaption = $dvdcaption;
                        $order->dvdcount = $cnt_order_dvds;
                        $order->status = 1;
                        $order->inserttime = time();
                        $order->updatetime = time();
                        $order->save();
                        $orderid = $order -> id;
                        
                        //Session::forget('orderid');
                        $uploadfile_counter = 0;
                        for ($i = 0; $i < 50; $i++) {

                            $file_key = Input::get('f' . ($i + 1));
                            $file_text = Input::get('t' . ($i + 1));

                            if(isset($file_key) && !empty(trim($file_key))){
                                $f = new FileModel;
                                $f->uid = $user->id;
                                $f->ftype = self::getMediaType($file_key);
                                $f->furl = "https://s3.amazonaws.com/burunvideo/". $file_key;
                                $f->ftsurl = $file_key;
                                $f->fzipurl = '';
                                $f->fplaytime = 0;
                                $f->fweight = 1;
                                $f->ct_caption = $file_text?$file_text:"";
                                $f->finserttime = time();
                                $f->file_index = $uploadfile_counter;
                                $f->save();

                                $file_ids[$uploadfile_counter] = $f->id;

                                $ofile = new OrderFile;
                                $ofile->fid = $f->id;
                                $ofile->oid = $orderid;
                                $ofile->save();

                                $uploadfile_counter ++;
                            }
                        }

                        $file_count = $uploadfile_counter;  
                        
                        $order->filecount = $file_count;
                        $order->save();                                                                      
                        
                        foreach ($shipping as $item) {
                            $ship = new OrderShipping;
                            $ship->orderid = $order->id;
                            $ship->firstname = $item['firstname'];
                            $ship->lastname = $item['lastname'];
                            $ship->street = $item['street'];
                            $ship->city = $item['city'];
                            $ship->state = $item['state'];
                            $ship->zipcode = $item['zipcode'];
                            $ship->dvdcount = $item['count'];
                            $ship->save();
                        }

                        ///-------- create order - end ------------
                        // update user table
//                $user->mon_weight = $user->mon_weight - $slotcount;
//                $user->save();
//
                        //create promocode history
                        if ($promo['id']) {
                            $data = [
                                'order_id' => $order->id,
                                'promocode_id' => $promo['id'],
                                'uid' => $uid
                            ];
                            $promo_code = PromoCodeUse::create($data);
                        }

                        //---------- insert pay history ---- begin----------

                        $pay_history = new Transaction;
                        $pay_history->uid = $uid;
                        $pay_history->oid = $order->id;
                        $pay_history->ttype = 1;
                        $pay_history->price = floatval($pay_price);
                        $pay_history->promocode_id = $promo['id'];
                        $pay_history->final_price = floatval($final_price);
                        $pay_history->payorg = 0;
                        $pay_history->paytime = time();
                        $pay_history->save();
                        //---------- insert pay history ---- end----------
                    } else {
                        $oldOrder = Order::find($preorderid);

                        $order = new Order;
                        $order->uid = $oldOrder->uid;
                        $order->orderTag = $orderTag;
                        $order->filecount = $oldOrder->filecount;
                        $order->weight = $oldOrder->weight;
                        $order->zipurl = $oldOrder->zipurl;
                        $order->devicetype = 0; //$devicetype;
                        $order->dvdtitle = $dvdtitle;
                        $order->dvdcaption = $dvdcaption;
                        $order->inserttime = time();
                        $order->updatetime = time();
                        $order->status = 0;
                        $order->preorder = $preorderid;
                        $order->save();

                        foreach ($shipping as $item) {
                            $ship = new OrderShipping;
                            $ship->orderid = $order->id;
                            $ship->firstname = $item['firstname'];
                            $ship->lastname = $item['lastname'];
                            $ship->street = $item['street'];
                            $ship->city = $item['city'];
                            $ship->state = $item['state'];
                            $ship->zipcode = $item['zipcode'];
                            $ship->dvdcount = $item['count'];
                            $ship->save();
                        }

                        //create promocode history
                        if ($promo['id']) {
                            $data = [
                                'order_id' => $order->id,
                                'promocode_id' => $promo['id'],
                                'uid' => $uid
                            ];
                            $promo_code = PromoCodeUse::create($data);
                        }

                        // get preorder's file
                        $files = DB::select('select f.* from file f '
                                        . ' inner join order_file of on of.fid = f.id '
                                        . ' inner join orders o on o.id = of.oid '
                                        . ' where o.id = ' . $preorderid);
                        foreach ($files as $file) {
                            // insert order_file relation
                            $ofile = new OrderFile;
                            $ofile->fid = $file->id;
                            $ofile->oid = $order->id;
                            $ofile->save();
                        }

                        //$order->status  = 1;
                        $order->status = 2;
                        $order->save();

                        $paytran = new Transaction;
                        $paytran->uid = $uid;
                        $paytran->oid = $order->id;
                        $paytran->ttype = 1;
                        $paytran->price = floatval($pay_price);
                        $paytran->promocode_id = $promo['id'];
                        $paytran->final_price = floatval($final_price);
                        $paytran->paytime = time();
                        $paytran->save();
                    }


        			//mail confirm///
                    $orderid = $order->id;
                    $confirmOrderNumber = sprintf("A%'.08d", $orderid);
        			$mailReceiveUserName = ucfirst(strtolower($user->first_name)) . ' ' . ucfirst(strtolower($user->last_name));
        			$mailReceiverEmail = $user->email;
        			
        			$subject = "Burn Video Order #".$confirmOrderNumber." Confirmation";
        			
        			$orderBillingInfo = $mailReceiveUserName . "<br/>";
        			$orderBillingInfo .= strip_tags($user->street) . "  " . strip_tags($user->city) . "<br/>";
        			$orderBillingInfo .= strip_tags($user->state) . " " . strip_tags($user->zipcode) . "<br/>";
        
        			$orderInfo = Order::find($orderid);
        			$orderDateTime = date("n-j-Y g:i A", $orderInfo->inserttime);
        			$orderShipping = DB::select('select n.* from order_shipping n where n.orderid=?', [$orderid]);
        			
        			$orderShippingInfo = "";
        			$totalAmount = 0;
        			foreach($orderShipping as $eachItem){
        				
        				$itemAddress = ucfirst(strtolower($eachItem->firstname)) . ' ' . ucfirst(strtolower($eachItem->lastname)) . "<br/>";
        				$itemAddress .= strip_tags($eachItem->street) . " " . strip_tags($eachItem->city) . "<br/>";
        				$itemAddress .= strip_tags($eachItem->state) . " " . strip_tags($eachItem->zipcode) . "<br/>";
        				
        				$itemDvd = $eachItem->dvdcount;
        				$subPrice = number_format((float)$itemDvd * 5.99, 2, '.', '');
        				
        				$oneItem = "<tr class='bb'><td>" . $itemAddress . "</td><td align='right'>" . $itemDvd ."</td><td align='right'>$" . $subPrice . "</td></tr>";
        				$orderShippingInfo .= $oneItem;
        				
        				$totalAmount += $subPrice;
        			}
        			
        			$orderTransaction = Transaction::where('oid', $orderid)->first();
        			
        			$totalPrice = $orderTransaction->final_price;
        			$orderPromoId = $orderTransaction->promocode_id;
        			$orderPromo = PromoCode::find($orderPromoId);
        			
        			$promoCode = null;
        			if ( !empty($orderPromo) ){
        				$promoCode = $orderPromo->name;
        			}
        			
        			$discountPrice = $totalAmount - $totalPrice;
        			$data = array(
        				'username' => $mailReceiveUserName,
        				'ordernum' => $confirmOrderNumber,
        				'orderDateTime' => $orderDateTime,
        				'orderBilling' => $orderBillingInfo,
        				'orderShippingsPrice' => $orderShippingInfo,
        				'promoCode' => $promoCode,
        				'discountPrice' => $discountPrice,
        				'totalPrice' => $totalPrice,
        			);
        			$senderAdress = "info@burnvideo.net";
        
        			Mail::send('emails.confirmshippingorder', $data, function($mail) use ($senderAdress, $mailReceiverEmail, $mailReceiveUserName, $subject) {
        				$mail->from($senderAdress);
        				$mail->to($mailReceiverEmail, $mailReceiveUserName)
        				->subject($subject);
        
        			});

                    //mail confirm///

                    event(new CreatedOrderEvent((string)$order->id));

                    return Response::json([
                        'retcode' => 200,
                        'orderid' => $order->id,
                        'msg' => 'Thank you for your purchase. Your custom HD DVD is on its way. ']
                    );
                } else {
                  	Log::info(print_r($result, true));
                    return Response::json([ 'retcode' => 201, 'msg' => 'Payment Fail' /* , 'debug' => print_r($result, true) */]);
                }
            } else {
                return Response::json([ 'retcode' => 201, 'msg' => 'Payment Fail - Final Price cannot be less then zero']);
            }
        } catch (Exception $e) {
          	Log::info(print_r($e->getMessage(), true));
            return Response::json(['retcode' => 202, 'msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    public function getSettings() {
        $user = Sentinel::getUser();

        try {
            return view('front.settings')
                            ->with('user', $user)
                            ->with('states', $this->states);
        } catch (Exception $e) {
            return view('front.settings')
                            ->with('user', $user)
                            ->with('states', $this->states)
                            ->withErrors(array
                                ('message' => $e->getMessage()));
        }
    }

    public function postSettings() {
        $user = Sentinel::getUser();
        $rules = array(
            'email' => 'required|email|max:50|unique:users,email,' . $user->id,
            'password' => 'required|min:4',
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required'
        );

        $credentials = array(
            'email' => Input::get('email'),
            'password' => Input::get('password'),
            'first_name' => Input::get('first_name'),
            'last_name' => Input::get('last_name'),
            'street' => Input::get('street'),
            'city' => Input::get('city'),
            'state' => Input::get('state'),
            'zipcode' => Input::get('zipcode')
        );

        $validation = Validator::make($credentials, $rules);

        if ($validation->fails()) {
            return Redirect::to('settings')->withErrors($validation)->withInput();
        }

        try {
            $email = Input::get('email');
            $new_password = Input::get('password');
            $first_name = Input::get('first_name');
            $last_name = Input::get('last_name');
            $street = Input::get('street');
            $city = Input::get('city');
            $state = Input::get('state');
            $zipcode = Input::get('zipcode');

            // Update the user
            $user->email = $email;
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->street = $street;
            $user->city = $city;
            $user->state = $state;

            $user->zipcode = $zipcode;
            $user->save();

            $user = Sentinel::findById($user->id);
            //$resetCode = $user->getResetPasswordCode();
            if ( Sentinel::update($user, array('password' => $new_password)) ){
                return Redirect::to('settings')->with('success', 'Profile successfully updated.');                
            } else {
                return Redirect::to('settings')->withErrors(array('message' => 'Password reset failed.'));                
            }
//            if ($user->checkResetPasswordCode($resetCode)) {
//                
//                if ($user->attemptResetPassword($resetCode, $new_password)) {
//                    return Redirect::to('settings')->with('success', 'Profile successfully updated.');
//                } else {
//                    return Redirect::to('settings')->withErrors(array('message' => 'Password reset failed.'));
//                }
//            } else {
//                return Redirect::to('settings')->withErrors(array('message' => 'The provided password reset code is Invalid.'));
//            }
        } catch (Exception $e) {
            return Redirect::to('settings')->withErrors(array('message' => 'Login field is required.'));
        }
    }

    public function getLogin() {
        $user = Sentinel::getUser();

        try {
            if (Sentinel::check()) {
                $userGroup = Sentinel::findRoleByName('User');

                if ($user->inRole($userGroup)) {
                    return Redirect::to('/order');
                } else {
                    return view('front.login');
                }
            } else {
                return view('front.login');
            }
        } catch (Exception $e) {
            return view('front.login')->withErrors(array('message' => $e->getMessage()));
        }
    }

    public function postLogin() {
        $input = Input::all();
        $rules = array(
            'email' => 'required|email|max:50',
            'password' => 'required',
            //'captcha' => 'required|captcha'
			'g-recaptcha-response' => 'required|recaptcha'
        );


        $messages = array('g-recaptcha-response' => 'The :attribute is incorrect.',);

        $credentials = array(
            'email' => $input['email'],
            'password' => $input['password'],
        );

        $validation = Validator::make($input, $rules, $messages);
        if ($validation->fails()) {
            return Redirect::to('login')->withErrors($validation)->withInput();
        }

//        try {
        $user = Sentinel::authenticate($credentials, false);

        if ( !$user ) {
            return Redirect::to('login')->withErrors(array('message' => 'Login failed, unknown email or password.'));            
        }
        
        if (Sentinel::check()) {
            $userGroup = Sentinel::findRoleByName('User');

            if ($user->inRole($userGroup)) {
                return Redirect::to('/order');
            } else {
                return Redirect::to('login')->withErrors(array('message' => 'You are not User.'));
            }
        } else {
            return Redirect::to('login')->withErrors(array('message' => 'Login failed, unknown email or password.'));
		}
//        } catch (Cartalyst\Sentinel\Users\LoginRequiredException $e) {
//            return Redirect::to('login')->withErrors(array('message' => 'Login field is required.'));
//        } catch (Cartalyst\Sentinel\Users\PasswordRequiredException $e) {
//            return Redirect::to('login')->withErrors(array('message' => 'Password field is required.'));
//        } catch (Cartalyst\Sentinel\Users\WrongPasswordException $e) {
//            return Redirect::to('login')->withErrors(array('message' => 'Wrong password, try again.'));
//        } catch (Cartalyst\Sentinel\Users\UserNotFoundException $e) {
//            return Redirect::to('login')->withErrors(array('message' => 'User was not found.'));
//        } catch (Cartalyst\Sentinel\Users\UserNotActivatedException $e) {
//            return Redirect::to('login')->withErrors(array('message' => 'User is not activated.'));
//        } catch (Cartalyst\Sentinel\Throttling\UserSuspendedException $e) {
//            return Redirect::to('login')->withErrors(array('message' => 'User is suspended.'));
//        } catch (Cartalyst\Sentinel\Throttling\UserBannedException $e) {
//            return Redirect::to('login')->withErrors(array('message' => 'User is banned.'));
//        }
    }

    public function getLogout() {
        Session::forget("orderid");
        Sentinel::logout();
        return Redirect::to('/');
    }

    public function getHow() {
        $user = Sentinel::getUser();
        if (empty($user)) {
            return view('landing.how')
                            ->with('user', $user);
        } else {
            return view('front.how')
                            ->with('user', $user);
        }
    }
    
    public function getAbout() {
        $user = Sentinel::getUser();
        if (empty($user)) {
            return view('landing.about')
                            ->with('user', $user);
        } else {
            return view('front.about')
                            ->with('user', $user);
        }
    }

    public function getFaq() {
        $user = Sentinel::getUser();
        $faq = DB::select('SELECT s_value FROM settings WHERE s_name="faq"');
        if (empty($user)) {
            return view('landing.faq')
                            ->with('user', $user)
                            ->with('faq', $faq[0]->s_value);
        } else {
            return view('front.faq')
                            ->with('user', $user)
                            ->with('faq', $faq[0]->s_value);
        }
    }

    public function getBlog() {
        $user = Sentinel::getUser();
        $categories = Category::all();
        $post = Post::isPublished()
                ->orderBy('created_at','desc')
                ->first();
                
        $recent_posts = Post::orderBy('created_at','desc')
                    ->paginate(5);


        if (empty($user)) {
            return view('landing.blog')
                ->with('user', $user)
                ->withPost($post)
                ->with('recent_posts', $recent_posts)
                ->withCategories($categories)
                ->withPageTitle(config('blog.title'));
        } else {
            return view('front.blog')
                ->with('user', $user)
                ->withPost($post)
                ->with('recent_posts', $recent_posts)
                ->withCategories($categories)
                ->withPageTitle(config('blog.title'));                
        }
    }
    public function showBlog($slug) {
        $user = Sentinel::getUser();
        $post = Post::isPublished()
                ->whereSlug($slug)
                ->first();        
        $categories = Category::all();
        //$posts = Post::isPublished()->paginate(10);
//        $recent_posts = Post::where('category_id', $post->category_id)
//                    ->orderBy('created_at','desc')
//                    ->paginate(5);
        $recent_posts = Post::orderBy('created_at','desc')
                    ->paginate(5);
        
        if (empty($user)) {
//            return view('post.show')
//                ->with('user', $user)
//                ->withPageTitle($post->title)
//                ->withPost($post)
//                ->withCategories($categories)
//                ->withActiveCategory($post->category->id);        
            return view('landing.blog')
                ->with('user', $user)
                ->withPost($post)
                ->with('recent_posts', $recent_posts)
                ->withCategories($categories)
                ->withPageTitle(config('blog.title'));                
        } else {
//            return view('front.post.show')
//                ->with('user', $user)
//                ->withPageTitle($post->title)
//                ->withPost($post)
//                ->withCategories($categories)
//                ->withActiveCategory($post->category->id);                 
            return view('front.blog')
                ->with('user', $user)
                ->withPost($post)
                ->with('recent_posts', $recent_posts)
                ->withCategories($categories)
                ->withPageTitle(config('blog.title'));
        }
    }

    public function getPolicies() {
        $user = Sentinel::getUser();
        $policies = DB::select('SELECT s_value FROM settings  WHERE s_name="policies"');
        if (empty($user)) {
            return view('landing.policies')
                            ->with('user', $user)
                            ->with('policies', $policies[0]->s_value);
        } else {
            return view('front.policies')
                            ->with('user', $user)
                            ->with('policies', $policies[0]->s_value);
        }
    }
    
    public function getTerms() {
        $user = Sentinel::getUser();
        $terms = DB::select('SELECT s_value FROM settings  WHERE s_name="terms"');
        if (empty($user)) {
            return view('landing.terms')
                            ->with('user', $user)
                            ->with('terms', $terms[0]->s_value);
        } else {
            return view('front.terms')
                            ->with('user', $user)
                            ->with('terms', $terms[0]->s_value);
        }
    }

    public function getContact() {
        $user = Sentinel::getUser();
        if (empty($user)) {
            return view('landing.contact')
                            ->with('user', $user);
        } else {
            return view('front.contact')
                            ->with('user', $user);
        }
    }

    public function postContact() {
        $rules = array(
            'email' => 'required|email|max:50',
            'name' => 'required',
            'message' => 'required',
            //'captcha' => 'required|captcha'
			'g-recaptcha-response' => 'required|recaptcha'
        );

        $credentials = array('email' => Input::get('email'),
            'name' => Input::get('name'),
            'message' => Input::get('message'),
            //'captcha' => Input::get('captcha')
            'g-recaptcha-response' => Input::get('g-recaptcha-response')
        );

        $messages = array(
            'g-recaptcha-response' => 'The :attribute is incorrect.',
        );
        $validation = Validator::make($credentials, $rules, $messages);

        if ($validation->fails()) {
            return Redirect::to('contact')->withErrors($validation)->withInput();
        }
        try {
            $email = Input::get('email');
            $name = Input::get('name');
            $subject = Input::get('subject');
            $msg = Input::get('message');

            Mail::send('emails.contact', array('from' => $email, 'subject' => $subject, 'msg' => $msg), function($message) use ($email) {
                $message->to(config('services.contact_email'))->subject('Burn Video Visitor contact you!');
            });
            return Redirect::to('contact')->with('success', 'Email successfully sent to administrators.');
        } catch (Exception $e) {
            return Redirect::to('contact')->withErrors(array('message' => $e->getMessage()));
        }
    }

    public function getOrder() {
        $user = Sentinel:: getUser();
        $dvd_per_month = config('services.dvd-per-month');

        self::initBrainTree();

        $clientToken = Braintree_ClientToken::generate();

        return view('front.order')
                        ->with('user', $user)
                        ->with('dvd_per_month', $dvd_per_month)
                        ->with('states', $this->states)
                        ->with('clientToken', $clientToken)
                        ->with('mon_nextday', !empty($user->mon_nextday) ? $user->mon_nextday : '0');
    }

    public function getSignup() {
        self::initBrainTree();
        $clientToken = Braintree_ClientToken::generate();

        return view('front.signup')
                        ->with('states', $this->states)
                        ->with('clientToken', $clientToken);
    }

    public function postSignup() {
        
        // if (Input::get('agree') != 'on') {
        //     return Redirect::to('signup')->withErrors(array('message' => 'You must agree with Terms of Use.'))->withInput();
        // }
        
        $input = Input::all();
		/*
        $rules = array(
            'email' => 'required|email|max:50|confirmed|unique:users',
            'password' => 'required|min:4|confirmed',
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required',
            'nonce' => 'required',
            'captcha' => 'required|captcha'
        );*/

        $rules = array(
            'email' => 'required|email|max:50|confirmed|unique:users',
            'password' => 'required|min:4|confirmed',
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required',
            //'captcha' => 'required|captcha'
			'g-recaptcha-response' => 'required|recaptcha'
        );


        $messages = array( 'g-recaptcha-response' => 'The :attribute is incorrect.',);

 
        $credentials = array(
            'email' => Input::get('email'),
            'email_confirmation' => Input::get('confirm_email'),
            'password' => Input::get('password'),
            'password_confirmation' => Input::get('confirm_password'),
            'first_name' => Input::get('first_name'),
            'last_name' => Input::get('last_name'),
            'street' => Input::get('street'),
            'city' => Input::get('city'),
            'state' => Input::get('state'),
            'zipcode' => Input::get('zipcode')
        );

        $data = $credentials;
        //$data['captcha'] = $input['captcha'];
        $data['g-recaptcha-response'] = $input['g-recaptcha-response'];


        $validation = Validator::make($data, $rules, $messages);

        if ($validation->fails()) {
            return Redirect::to('signup')->withErrors($validation)->withInput();
        }
        $chk = Mailgun::validator()->validate($credentials['email']);
        if ($chk->is_valid == false) {
            return Redirect::to('signup')->withErrors(array('message' => 'Email address you provided is not valid'))->withInput();
        }


        try {
            self::initBrainTree();

            $email = Input::get('email');
            $password = Input::get('password');
            $first_name = Input::get('first_name');
            $last_name = Input::get('last_name');
            $street = Input::get('street');
            $city = Input::get('city');
            $state = Input::get('state');
            $zipcode = Input::get('zipcode');
            //$nonce = Input::get('payment_method_nonce');

            // Create the user
			/*
            $user = Sentinel::register(array(
                        'email' => $email,
                        'password' => $password,
                        'activated' => true,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'street' => $street,
                        'city' => $city,
                        'state' => $state,
                        'zipcode' => $zipcode,
                        'mon_weight' => 40
            ));*/
			$credentials = [
				'email' => $email,
				'password' => $password,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'street' => $street,
				'city' => $city,
				'state' => $state,
				'zipcode' => $zipcode,
				'mon_weight' => 40
            ];
			//var_dump($credentials);
			
            $user = Sentinel::registerAndActivate($credentials);

			
            // Find the group using the group id
            //$userGroup = Sentinel::findGroupByName('User');
            $userGroup = Sentinel::findRoleByName('User');

            // Assign the role to the user
            //$user->addGroup($userGroup);
			$userGroup->users()->attach($user);

            // login the user
            Session::forget("orderid");
            $credentials = [ 'email' => $email, 'password' => $password];
            $user = Sentinel::authenticate($credentials, false);


            return Redirect::to('login');
        } catch (Cartalyst\Sentinel\Users\LoginRequiredException $e) {
            return Redirect::to('signup')->withErrors(array('message' => 'Login field is required.'));
        } catch (Cartalyst\Sentinel\Users\PasswordRequiredException $e) {
            return Redirect::to('signup')->withErrors(array('message' => 'Password field is required.'));
        } catch (Cartalyst\Sentinel\Users\UserExistsException $e) {
            return Redirect::to('signup')->withErrors(array('message' => 'User with this login already exists.'));
        } catch (Cartalyst\Sentinel\Groups\GroupNotFoundException $e) {
            return Redirect::to('signup')->withErrors(array('message' => 'Group was not found.'));
        }
    }

    public function getForgot() {
        return view('front.forgot');
    }

    public function postForgot() {
        $rules = array(
            'email' => 'required|email|max:50'
        );

        $credentials = array(
            'email' => Input::get('email')
        );

        $validation = Validator::make($credentials, $rules);

        if ($validation->fails()) {
            return Redirect::to('forgot')->withErrors($validation)->withInput();
        }

//        try {
//            $new_password = str_random(6);
//            $user = Sentinel::findUserByLogin(Input::get('email'));
//            $user = User::where('email' , '=', Input::get('email'))->first();
//            $resetCode = $user->getResetPasswordCode();
//
//            if ($user->checkResetPasswordCode($resetCode)) {
//
//                if ($user->attemptResetPassword($resetCode, $new_password)) {
//
//                    Mail::send('emails.forgot', array('new_password' => $new_password), function($message) {
//                        $message->from('info@burnvideo.net');
//                        $message->to(Input::get('email'))->subject('Reset Password!');
//                    });
//                } else {
//                    return Redirect::to('forgot')->withErrors(array('message' => 'Password reset failed.'));
//                }
//            } else {
//                return Redirect::to('forgot')->withErrors(array('message' => 'The provided password reset code is Invalid.'));
//            }
//        } catch (Cartalyst\Sentinel\Users\UserNotFoundException $e) {
//            return Redirect::to('forgot')->withErrors(array('message' => 'User was not found.'));
//        } catch (Exception $e) {
//            return Redirect::to('forgot')->withErrors(array('message' => $e->getMessage()));
//        }

        $new_password = str_random(6);
        $user = User::where('email' , '=', Input::get('email'))->first();        
        if ($user){
            $userId = $user->id;
            
            $user = Sentinel::findById($userId);
            //$resetCode = $user->getResetPasswordCode();
            if ( Sentinel::update($user, array('password' => $new_password)) ){
                Mail::send('emails.forgot', array('new_password' => $new_password), function($message) {
                    $message->from('info@burnvideo.net');
                    $message->to(Input::get('email'))->subject('Reset Password!');
                });

            } else {
                return Redirect::to('forgot')->withErrors(array('message' => 'Password reset failed.'));                
            }
            
        } else {
            return Redirect::to('forgot')->withErrors(array('message' => 'User was not found.'));            
        }

        return Redirect::to('forgot')->with('success', 'Password reset was successful. Please check your email for temporary password.');
    }

    public function checkPromo() {
        $input = Input::all();
        //check promocode is valid
		
        $promo = PromoCode::where('name', $input['name']);

        if ($promo->count() == 0) {
            return Response::json(['status' => 0, 'message' => 'Invalid Promocode']);
        }
        $promo = $promo->first()->toArray();
        $today = date('Y-m-d');
		$promo_expire_date = $promo['expiry_date'];
		$tmp_expire_date = explode("-", $promo_expire_date);
		list($expiremonth, $expireday, $expireyear) = explode('-', $promo_expire_date);
		$promo_expire_date = "$expireyear-$expiremonth-$expireday";
		$promo_expire_date = strtotime($promo_expire_date);
        if ($promo_expire_date < strtotime($today)) {
            return Response::json([ 'status' => 0, 'message' => 'Promocode is expire']);
        }
        $deductVal = 0;
        if ($promo['type'] == 'value') {
            $deductVal = $promo['value'];
        } else {
            $deductVal = ($input['value'] * $promo['value']) / 100;
        }
        $finalVal = $input['value'] - $deductVal;
        if ($finalVal >= 0) {
            return Response::json(['status' => 1, 'value' => $deductVal]);
        } else {
            return Response::json(['status' => 0, 'message' => 'Invalid Promocode']);
        }
    }

    public function postFileDelete() {
        if (Input::has('fileid')) {
            $file = FileModel::find(Input::get('fileid'));
            unlink($file->furl);
            $file->delete();
        }
    }

}
