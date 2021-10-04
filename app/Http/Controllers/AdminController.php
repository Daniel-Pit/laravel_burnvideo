<?php
namespace burnvideo\Http\Controllers;

use Sentinel;
use DB;
use Input;
use Validator;
use Redirect;
use Session;
use Response;
use Image;

use burnvideo\Models\PromoCode;
use burnvideo\Models\MessageModel;
use burnvideo\Models\MessageUserModel;
use burnvideo\Models\User;
use burnvideo\Models\S3HistoryModel;
use burnvideo\Models\NotifyModel;
use burnvideo\Models\NotifyUserModel;
use burnvideo\Models\Order;
use burnvideo\Models\Setting;
use burnvideo\Models\CalendarEventModel;
use burnvideo\Models\Post;
use burnvideo\Models\Category;
use burnvideo\Models\Option;

class AdminController extends BaseController {

	/* error code */

	const ERR_SUCCESS = '0';
	const ERR_UNKNOWN = '-1';
	const ERR_FAILAUTH = '-2';
	const ERR_EXISTNAME = '-101';
	const ERR_EXISTMAIL = '-102';
	const ERR_FAILLOGIN = '-103';
	const ERR_INVALIDFILE = '-104';
	const ERR_INVALIDPARAM = '-105';
	const ERR_FAILBRAINTREE = '-110';
	const ERR_INSUFFICIENTWEIGHT = '-120';
	const ERR_FAILPAY = '-130';
	const ERR_FAILMONTHLYPAY = '-131';
	const ERR_NOPAYMENTSETTING = '-140';
	const ERR_RESETPASSWORDFAILED = '-150';
	const ERR_INVALIDPASSWORDCODE = '-151';

	/* error string */
	const MSG_SUCCESS = 'Success';
	const MSG_ERR_INVALIDPARAM = 'Invalid parameter';
	const MSG_ERR_UNKNOWN = 'Unknown Error';
	const MSG_ERR_FAILAUTH = 'Fail Authentication';
	const MSG_ERR_EXISTNAME = 'Name Already Exists';
	const MSG_ERR_EXISTMAIL = 'Mail Already Exists';
	const MSG_ERR_FAILLOGIN = 'Login Fail';
	const MSG_ERR_INVALIDFILE = 'Invalid file';
	const MSG_ERR_INSUFFICIENTWEIGHT = 'Insufficient slot count';
	const MSG_ERR_FAILPAY = 'Fail Payment';
	const MSG_ERR_FAILMONTHLYPAY = 'Fail Monthly Payment';
	const MSG_ERR_NOPAYMENTSETTING = 'No setting payment';
	const MSG_ERRRESETPASSWORDFAILED = 'Password reset failed.';
	const MSG_INVALIDPASSWORDCODE = 'The provided password reset code is Invalid.';
	const MSG_INVALIDFILETYPE = 'Invalid File type.';



	const FIREBASE_API_KEY = 'AAAA0B0xmZc:APA91bGW9dhIvCxQeFTPl54u6bPgMvrXUp06RTgru6qnJiujrxLINkU4PaFcRER4cu9_p82iGbwr0HQiMi0WbCJYLl4epRot-tPrETFblWwCrYhxDmFhb7sjULD2LQF89xk7mZBTpu_H';
	//const FIREBASE_API_KEY = 'AIzaSyARjklTTBau5w2a0LzVH46Lx5SqfdQtFV4';
	const FIREBASE_SENDER_ID = '893842987415';

    //////////////////////////////////////
    // Actions
    //////////////////////////////////////

    public function getIndex() {
        $user = Sentinel::getUser();

        try {
            //$ordercnts = DB::select('select count(*) as cnt from orders where status in (0, 1)');
            $ordercnts = DB::select('select count(*) as cnt from orders where status in (1)');
            $files = DB::select('select count(*) as cnt from file');
            $usercount = DB::select('select count(*) as cnt from users u inner join role_users ug on u.id = ug.user_id where ug.role_id = 2');
            //$earned = DB::select('select sum(final_price) as price from transaction WHERE YEAR(created_at) = YEAR(CURDATE());');
            $earned = 0;

            /*
              $goodusers = DB::select(" select sum(price) as price, uid, u.first_name, u.last_name, u.state from transaction t "
              . " left join users u on t.uid = u.id "
              . " group by t.uid order by price desc limit 7 ");
             */
            $topzipcode = DB::select("select u.zipcode as ordertag, sum(o.dvdcount) as dvdcount from orders o inner join users u on u.id = o.uid "
                            . "where o.status in (2, 3) and dvdcount > 0 group by u.zipcode order by dvdcount desc limit 7 ");
            $topcount = count($topzipcode);

            $newusers = DB::select(" select u.first_name, u.last_name, u.state, u.created_at from users u "
                            . " inner join role_users ug on u.id = ug.user_id where ug.role_id = 2 "
                            . " order by u.created_at desc limit 4 ");

            //$orders = DB::select('select o.*, u.first_name, u.last_name from orders o '
            //                . ' left join users u on u.id = o.uid  where o.status in (0, 1) order by o.inserttime desc limit 4');

            $orders = DB::select('select o.*, u.first_name, u.last_name from orders o '
                            . ' left join users u on u.id = o.uid  where o.status in (1) order by o.inserttime desc limit 4');

            foreach ($orders as $key => &$item) {
                $item->inserttime = date('n-j-Y g:i A', $item->inserttime);
            }

            $earn_pc = DB::select('select sum(t.final_price) as price from transaction t inner join orders o on t.oid = o.id where o.devicetype = 0');
            $earn_ios = DB::select('select sum(t.final_price) as price from transaction t inner join orders o on t.oid = o.id where o.devicetype = 1');
            $earn_android = DB::select('select sum(t.final_price) as price from transaction t inner join orders o on t.oid = o.id where o.devicetype = 2');

            ////////////////////monthly earned graph///////////////////////////////////
            
            $current_year_num = date('Y');
            $current_month_num = date('m');
            $earned_data_monthly = [];
            for ( $month_i = 1; $month_i <= $current_month_num; $month_i ++){
                if ( $month_i < 10 ) {
                    $selected_year_month = $current_year_num . "-0" . $month_i;
                } else {
                    $selected_year_month = $current_year_num . "-" . $month_i;
                }

                //$earned = DB::select('select sum(final_price) as price from transaction WHERE YEAR(created_at) = YEAR(CURDATE());');
                $earn_data_pc_monthly = DB::select('select sum(t.final_price) as price from transaction t inner join orders o on t.oid = o.id where o.devicetype = 0 and DATE_FORMAT(t.created_at, "%Y-%m") = "'.$selected_year_month .'"');
                $earn_data_ios_monthly = DB::select('select sum(t.final_price) as price from transaction t inner join orders o on t.oid = o.id where o.devicetype = 1 and DATE_FORMAT(t.created_at, "%Y-%m") = "'.$selected_year_month .'"');
                $earn_data_android_monthly = DB::select('select sum(t.final_price) as price from transaction t inner join orders o on t.oid = o.id where o.devicetype = 2 and DATE_FORMAT(t.created_at, "%Y-%m") = "'.$selected_year_month . '"');
                
                $earn_pc_monthly = isset($earn_data_pc_monthly[0])?$earn_data_pc_monthly[0]->price:0;
                $earn_ios_monthly = isset($earn_data_ios_monthly[0])?$earn_data_ios_monthly[0]->price:0;
                $earn_android_monthly = isset($earn_data_android_monthly[0])?$earn_data_android_monthly[0]->price:0;
                
                $earn_total_monthly = $earn_pc_monthly + $earn_ios_monthly + $earn_android_monthly;
                
                $earned_data_month = [
                        "m" => $selected_year_month, 
                        "total" => number_format($earn_total_monthly, 2, '.', ''),
                        "iOS" => number_format($earn_ios_monthly, 2, '.', ''), 
                        "pc" => number_format($earn_pc_monthly, 2, '.', ''), 
                        "android" => number_format($earn_android_monthly, 2, '.', '')
                    ];
                    
                $earned_data_monthly[] = $earned_data_month;
                
                $earned += $earn_total_monthly;
            }
            
            ////////////////////monthly earned graph end!///////////////////////////////////
            
            ////////////////////monthly graph///////////////////////////////////
            

            $current_year_month_date = date("Y-m");
            $current_day_num = date('d');
            $order_data_daily = [];
            for ( $day_i = 1; $day_i <= $current_day_num; $day_i ++){
                if ( $day_i < 10 ) {
                    $selected_date = $current_year_month_date . "-0" . $day_i;
                } else {
                    $selected_date = $current_year_month_date . "-" . $day_i;
                }

                $ordercnts_pc_data = DB::select('select count(id) as cnt from orders where devicetype = 0 and DATE_FORMAT(created_at, "%Y-%m-%d") = "'.$selected_date .'"');
                $ordercnts_ios_data = DB::select('select count(id) as cnt from orders where devicetype = 1 and DATE_FORMAT(created_at, "%Y-%m-%d") = "'.$selected_date .'"');
                $ordercnts_android_data = DB::select('select count(id) as cnt from orders where devicetype = 2 and DATE_FORMAT(created_at, "%Y-%m-%d") = "'.$selected_date .'"');

                $ordercnt_pc_day = isset($ordercnts_pc_data[0])?$ordercnts_pc_data[0]->cnt:0;
                $ordercnt_ios_day = isset($ordercnts_ios_data[0])?$ordercnts_ios_data[0]->cnt:0;
                $ordercnt_android_day = isset($ordercnts_android_data[0])?$ordercnts_android_data[0]->cnt:0;
                
                $order_total_day = $ordercnt_pc_day + $ordercnt_ios_day + $ordercnt_android_day;
                
                $order_data_day = [
                        "date" => $selected_date, 
                        "total" => $order_total_day,
                        "iOS" => $ordercnt_ios_day, 
                        "pc" => $ordercnt_pc_day, 
                        "android" => $ordercnt_android_day
                    ];
                    
                $order_data_daily[] = $order_data_day;
            }

            ////////////////////monthly graph///////////////////////////////////
            
            return view('admin.index')
                            ->with('user', $user)
                            ->with('topcount', $topcount)
                            ->with('topzipcode', $topzipcode)
                            ->with('newusers', $newusers)
                            ->with('orders', $orders)
                            ->with('ordercount', $ordercnts[0]->cnt)
                            ->with('filecount', $files[0]->cnt)
                            ->with('usercount', $usercount[0]->cnt)
                            ->with('earned', number_format((float) $earned, 2, '.', ''))
                            ->with('earn_pc', $earn_pc[0]->price)
                            ->with('earn_ios', $earn_ios[0]->price)
                            ->with('earn_android', $earn_android[0]->price)
                            ->with('earn_month_data', json_encode($earned_data_monthly))
                            ->with('order_daily_data', json_encode($order_data_daily));
        } catch (Exception $e) {
            return view('admin.index')
                            ->with('user', $user)
                            ->withErrors(array('message' => $e->getMessage()));
        }

        return view('admin.index')
                        ->with('user', $user);
    }

    public function getLogin() {
        $user = Sentinel::getUser();

        try {
            if (Sentinel::check()) {
                $admin = Sentinel::findRoleByName('Admin');

                if ($user->inRole($admin)) {
                    return Redirect::to('admin');
                } else {
                    return view('admin.login');
                }
            } else {
                return view('admin.login');
            }
        } catch (Exception $e) {
            return view('admin.login')
                            ->withErrors(array('message' => $e->getMessage()));
        }
    }

    public function postLogin() {
        $rules = array(
            'email' => 'required|email|max:50',
            'password' => 'required',
        );

        $credentials = array(
            'email' => Input::get('email'),
            'password' => Input::get('password'),
        );

        $validation = Validator::make($credentials, $rules);

        if ($validation->fails()) {
            return Redirect::to('admin/login')->withErrors($validation)->withInput();
        }

		$user = Sentinel::authenticate($credentials, false);

        if ( !$user ) {
            return Redirect::to('admin/login')
				->withErrors(array('message' => 'Login failed, unknown email or password.'));          
        }
//        try {
            if (Input::get('rememberMe')) {
                $user = Sentinel::loginAndRemember($user);
            } else {
                $user = Sentinel::login($user);
            }

            if (Sentinel::check()) {
                $admin = Sentinel::findRoleByName('Admin');

                if ($user->inRole($admin)) {
                    return Redirect::to('admin');
                } else {
                    return Redirect::to('admin/login')
                                    ->withErrors(array('message' => 'You are not Administrator.'));
                }
            } else {
                return Redirect::to('admin/login')
					->withErrors(array('message' => 'You are not Administrator.'));

			}
//        } catch (Cartalyst\Sentinel\Users\LoginRequiredException $e) {
//            return Redirect::to('admin/login')->withErrors(array('message' => 'Login field is required.'));
//        } catch (Cartalyst\Sentinel\Users\PasswordRequiredException $e) {
//            return Redirect::to('admin/login')->withErrors(array('message' => 'Password field is required.'));
//        } catch (Cartalyst\Sentinel\Users\WrongPasswordException $e) {
//            return Redirect::to('admin/login')->withErrors(array('message' => 'Wrong password, try again.'));
//        } catch (Cartalyst\Sentinel\Users\UserNotFoundException $e) {
//            return Redirect::to('admin/login')->withErrors(array('message' => 'User was not found.'));
//        } catch (Cartalyst\Sentinel\Users\UserNotActivatedException $e) {
//            return Redirect::to('admin/login')->withErrors(array('message' => 'User is not activated.'));
//        } catch (Cartalyst\Sentinel\Throttling\UserSuspendedException $e) {
//            return Redirect::to('admin/login')->withErrors(array('message' => 'User is suspended.'));
//        } catch (Cartalyst\Sentinel\Throttling\UserBannedException $e) {
//            return Redirect::to('admin/login')->withErrors(array('message' => 'User is banned.'));
//        }
    }

    public function getLogout() {
        Sentinel::logout();
        return Redirect::to('admin/login');
    }

    public function userList() {
        $user = Sentinel::getUser();
        $users = DB::select('select u.* from users u '
                        . 'inner join role_users ug on u.id = ug.user_id '
                        . 'where ug.role_id = 2 '
                        . 'order by u.id asc');

        return view('admin.userList')
                        ->with('user', $user)
                        ->with('count', count($users))
                        ->with('users', $users);
    }

    public function mediaList() {
        $user = Sentinel::getUser();

        $medias = DB::select('select f.*, u.first_name, u.last_name from file f '
                        . 'inner join users u on u.id = f.uid order by f.id asc');

        foreach ($medias as $key => &$item) {
            $item->filename = "";
            $item->url = "";
            if ($item->fzipurl) {
                $item->filename = $item->fzipurl;
                $item->url = $item->fzipurl;
            } else if ($item->ftsurl) {
                $item->filename = $item->ftsurl;
                $item->url = $item->ftsurl;
            } else if ($item->furl) {
                $item->filename = $item->furl;
                $item->url = $item->furl;
            }

            $item->finserttime = date('n-j-Y g:i A', $item->finserttime);
        }

        return view('admin.mediaList')
                        ->with('user', $user)
                        ->with('count', count($medias))
                        ->with('medias', $medias);
    }

    public function calendar() {
        $user = Sentinel::getUser();

        $events = DB::select('select * from calendar_event where uid = ' . $user->id);

        return view('admin.calendar')
                        ->with('user', $user)
                        ->with('events', $events);
    }

    public function notifyList() {
        $user = Sentinel::getUser();

        $messages = DB::select('select n.* from notification n where n_sendtype = 0 order by n.nid asc');
        foreach ($messages as $key => &$message) {
            $message->time = date('n-j-Y g:i A', $message->n_time);

            $users = DB::select('select u.first_name, u.last_name, n.sendflag from notification_user n inner join users u on n.uid = u.id where n.nid=?', [$message->nid]);
            $userStr = '';
            foreach ($users as $key => $item) {
                if ($key > 0)
                    $userStr = $userStr . ', ';

                //$userStr = $userStr . $item->first_name . ' ' . $item->last_name;
				if($item->sendflag){
					$userStr = $userStr . "<font style='color:#5cb85c;'>". $item->first_name . ' ' . $item->last_name . "</font>";
				} else {
					$userStr = $userStr . $item->first_name . ' ' . $item->last_name;
				}
            }

            $message->users = $userStr;
        }

        return view('admin.notifyList')
                        ->with('user', $user)
                        ->with('count', count($messages))
                        ->with('notifies', $messages);
    }

    public function notifyListById() {
        $user = Sentinel::getUser();

        $messages = DB::select('select n.* from notification n where n_sendtype = 1 order by n.nid asc');
        foreach ($messages as $key => &$message) {
            $message->time = date('n-j-Y g:i A', $message->n_time);

            $users = DB::select('select u.first_name, u.last_name, n.sendflag from notification_user n inner join users u on n.uid = u.id where n.nid=?', [$message->nid]);
            $userStr = '';
            foreach ($users as $key => $item) {
                if ($key > 0)
                    $userStr = $userStr . ', ';

                //$userStr = $userStr . $item->first_name . ' ' . $item->last_name;

				if($item->sendflag){
					$userStr = $userStr . "<font style='color:#5cb85c;'>". $item->first_name . ' ' . $item->last_name . "</font>";
				} else {
					$userStr = $userStr . $item->first_name . ' ' . $item->last_name;
				}
            }

            $message->users = $userStr;
        }

        return view('admin.notifyListById')
                        ->with('user', $user)
                        ->with('count', count($messages))
                        ->with('notifies', $messages);
    }

    public function messageList() {
        $user = Sentinel::getUser();

        $messages = DB::select('select n.* from message n where m_sendtype = 0 order by n.mid asc');
        foreach ($messages as $key => &$message) {
            $message->time = date('n-j-Y g:i A', $message->m_time);

            $users = DB::select('select u.first_name, u.last_name, n.sendflag from message_user n inner join users u on n.uid = u.id where n.mid=?', [$message->mid]);
            $userStr = '';
            foreach ($users as $key => $item) {
                if ($key > 0)
                    $userStr = $userStr . ', ';

				if($item->sendflag){
					$userStr = $userStr . "<font style='color:#5cb85c;'>". $item->first_name . ' ' . $item->last_name . "</font>";
				} else {
					$userStr = $userStr . $item->first_name . ' ' . $item->last_name;
				}
            }

            $message->users = $userStr;
        }

        return view('admin.messageList')
                        ->with('user', $user)
                        ->with('messages', $messages);
    }

    public function messageListById() {
        $user = Sentinel::getUser();

        $messages = DB::select('select n.* from message n where m_sendtype = 1 order by n.mid asc');
        foreach ($messages as $key => &$message) {
            $message->time = date('n-j-Y g:i A', $message->m_time);

            $users = DB::select('select u.first_name, u.last_name, n.sendflag from message_user n inner join users u on n.uid = u.id where n.mid=?', [$message->mid]);
            $userStr = '';
            foreach ($users as $key => $item) {
                if ($key > 0)
                    $userStr = $userStr . ', ';

                if($item->sendflag){
                    $userStr = $userStr . "<font style='color:#5cb85c;'>". $item->first_name . ' ' . $item->last_name . "</font>";
                } else {
                    $userStr = $userStr . $item->first_name . ' ' . $item->last_name;
                }
            }

            $message->users = $userStr;
        }

        return view('admin.messageListById')
            ->with('user', $user)
            ->with('messages', $messages);
    }
    
    public function messageListByIds() {
        $user = Sentinel::getUser();

        $messages = DB::select('select n.* from message n where m_sendtype = 2 order by n.mid asc');
        foreach ($messages as $key => &$message) {
            $message->time = date('n-j-Y g:i A', $message->m_time);

            $users = DB::select('select u.first_name, u.last_name, n.sendflag from message_user n inner join users u on n.uid = u.id where n.mid=?', [$message->mid]);
            $userStr = '';
            foreach ($users as $key => $item) {
                if ($key > 0)
                    $userStr = $userStr . ', ';

                if($item->sendflag){
                    $userStr = $userStr . "<font style='color:#5cb85c;'>". $item->first_name . ' ' . $item->last_name . "</font>";
                } else {
                    $userStr = $userStr . $item->first_name . ' ' . $item->last_name;
                }
            }

            $message->users = $userStr;
        }

        return view('admin.messageListByIds')
            ->with('user', $user)
            ->with('messages', $messages);
    }
    
    
    public function s3Manage() {
        $user = Sentinel::getUser();

        $messages = DB::select('select s3.* from s3history as s3 order by id asc');


        return view('admin.s3manage')
                        ->with('user', $user)
                        ->with('count', count($messages))
                        ->with('histories', $messages);
    }


    public function api_deleteS3() {
        
        try{
            $input = Input::all();
            $from_order = $input['fromValue'];
            $to_order = $input['toValue'];
    
            if(!isset($from_order) || empty($from_order)){
                $from_order = 1;
            }
    
            if(!isset($to_order) || empty($to_order)){
                return Response::json([
                            'status' => false,
                            'message' => 'Specify an Order Number'
                ]);
            }
            
            $s3history = new S3HistoryModel;
            
            $s3history->from = $from_order;
            $s3history->to = $to_order;
            $s3history->executed_date = time();
            $s3history->save();
            
    
            //echo sprintf("var/www/html/php artisan s3bucket:delete %s %s", $to_order, $from_order)."\n";
    		//shell_exec(sprintf("var/www/html/php artisan s3bucket:delete %s %s", $to_order, $from_order));
    		//shell_exec(sprintf("php artisan s3bucket:delete %s %s", $to_order, $from_order));
    
            return Response::json([
                'status' => true
            ]);
            
        } catch(Exception $e){
            
            return Response::json([
                'status' => $e->getMessage()
            ]);
            
        }
    }
  
    public function transactionList() {
        $user = Sentinel::getUser();
        /*
          $transactions = DB::select('select t.*, o.id as oid, o.devicetype, u.first_name, u.last_name from transaction t '
          . ' left join orders o on o.id = t.oid '
          . ' left join users u on u.id = t.uid order by t.paytime desc ' );
         */
        $transactions = DB::select('select t.*,p.name,p.type,p.value, o.id as oid, o.devicetype, u.first_name, u.last_name, u.email from transaction t '
                        . ' left join orders o on o.id = t.oid '
                        . ' left join promocode p on p.id = t.promocode_id '
                        . ' inner join users u on u.id = t.uid order by t.paytime asc ');


        foreach ($transactions as $key => &$item) {
            $item->ordertag = empty($item->oid) ? "-" : sprintf("A%'.08d", $item->oid);

            if ($item->devicetype == 0) {
                $item->devicestr = "PC";
            } else if ($item->devicetype == 1) {
                $item->devicestr = "iOS";
            } else if ($item->devicetype == 2) {
                $item->devicestr = "Android";
            }

            if ($item->ttype == 0) {
                $item->devicestr = "-";
                $item->ttype = 'Auto Pay';
            } else {
                $item->ttype = 'DVD Ordered';
            }

            $item->price = sprintf("%.2f", $item->price);
            $item->final_price = sprintf("%.2f", $item->final_price);
        }
//        return $transactions;
        return view('admin.transactionList')
                        ->with('user', $user)
                        ->with('transactions', $transactions);
    }

    public function orderList() {
        $user = Sentinel::getUser();

        
        //$orders = DB::select('select o.*, u.first_name, u.last_name, u.first_ordertime from orders o '
        //  . ' left join users u on u.id = o.uid order by o.id asc ' );
         
		//successed request only
        $orders = DB::select('select o.*, u.first_name, u.last_name, u.first_ordertime from orders o '
                        . ' inner join users u on u.id = o.uid where o.status > 0 order by o.id desc limit 5000;');

        foreach ($orders as $key => &$item) {
            //$files = DB::select('select f.* from file f '
            //                . ' inner join order_file of on of.fid = f.id '
            //                . ' inner join orders o on o.id = of.oid '
            //                . ' where o.id = ' . $item->id);
            //$item->files = $files;
          	$item->firstOrderFlag = ($item->first_ordertime + 170) - $item->inserttime;
            if($item->inserttime < 1){
                $item->firstOrderFlag = 0;
            }          
            $item->inserttime = date('n-j-Y g:i A', $item->inserttime);
            $item->updatetime = date('n-j-Y g:i A', $item->updatetime);
        }

        return view('admin.orderList')
                        ->with('orders', $orders)
                        ->with('user', $user);
    }

    public function setting() {
        $user = Sentinel::getUser();

        $faq = DB::select('select s_value from settings s where s.s_name = "faq"');
        $policies = DB::select('select s_value from settings s where s.s_name = "policies"');
        $terms = DB::select('select s_value from settings s where s.s_name = "terms"');
        $burn = DB::select('select s_value from settings s where s.s_name = "burn"');

        $faqval = "";
        $policiesval = "";
        $termsval = "";
        $burnval = "";

        if (!empty($faq)) {
            $faqval = $faq[0]->s_value;
        }
        if (!empty($policies)) {
            $policiesval = $policies[0]->s_value;
        }
        if (!empty($terms)) {
            $termsval = $terms[0]->s_value;
        }
        if (!empty($burn)) {
            $burnval = $burn[0]->s_value;
        }


        return view('admin.setting')
                        ->with('user', $user)
                        ->with('faq', $faqval)
                        ->with('policies', $policiesval)
                        ->with('terms', $termsval)
                        ->with('burn', $burnval);
    }

    public function blog(){
        
        $user = Sentinel::getUser();
        
        $posts = Post::orderBy('created_at','desc')->paginate(10);
        return view('admin.blog')
            ->with('user', $user)
            ->withPosts($posts)
            ->withCategories(Category::all())
            ->withOptionRssName(Option::get('rss_name'))
            ->withOptionRssNumber(Option::get('rss_number'));        
    }
    
    public function createPost() {
        $user = Sentinel::getUser();
        $categories = Category::pluck('name', 'id');

        return view('admin.blogeditor')
            ->with('user', $user)
            ->withCategories($categories)
            ->withPostId('');
    }

    public function editPost($id) {
        $user = Sentinel::getUser();
        $categories = Category::pluck('name', 'id');
        $post= Post::find($id);

        return view('admin.blogeditor')
            ->with('user', $user)
            ->withCategories($categories)
            ->withPost($post)
            ->withPostId($id);
    }

    public function show($slug) {
        $user = Sentinel::getUser();
        $post = Post::whereSlug($slug)->first();

        return view('admin.blogshow')
            ->with('user', $user)
            ->withPost($post);
    }

    public function showPost($slug) {
        $post = Post::whereSlug($slug)->first();

        return view('admin.blogshow')
            ->withPost($post);
    }

    public function formAddImage($post_id) {
        $post = Post::find($post_id);

        return view('admin.blogimage')
            ->withPost($post);
    }

    public function addImage($post_id) {
        @mkdir(public_path().'/img/');
        @mkdir(public_path().'/img/posts/');
        @mkdir(public_path().'/img/posts/thumbs');

        $post = Post::find($post_id);

        $file = Input::file('image');
        $img = Image::make($file)->fit(1000, 500);

        $filename = '/img/posts/'.$file->getClientOriginalName();
        $img->save( public_path().$filename );

        $post->image = $file->getClientOriginalName();
        $post->save();

        return redirect('/admin/blog/');
    }

    public function ajax_post_save() {

        $fields = array_except(Input::all(), ['_token', 'post_id' ]);

        $fields['slug'] = (strlen($fields['slug']) === 0) ? str_slug($fields['title'], '-') : str_slug($fields['slug'], '-');

        if (Input::get('post_id') > 0) {
            $post = Post::find(Input::get('post_id'));
            $category_id = $post->category_id;
            if ( $fields['category_id'] != $category_id ){
                $new_category = Category::find($category_id);
                $new_category->posts_num += 1;
                $new_category->save();
                
                $old_category = Category::find($fields['category_id']);
                $old_category->posts_num -= 1;
                $old_category->save();                                
            }
            $post->update($fields);
        } else {
            $post = Post::create($fields);
            $category_id = $post->category_id;
            $category = Category::find($category_id);
            $category->posts_num += 1;
            $category->save();
        }

        return response()->json($post);
    }

    public function ajax_post_load() {
        $post_id = Input::get('post_id');


        $post = Post::find($post_id);
        return response()->json($post);
    }

    public function ajax_post_publish() {
        $post_id = Input::get('post_id');

        $post = Post::find($post_id);
        if ($post->published_at === '0000-00-00 00:00:00' || empty($post->published_at)) {
            $post->published_at = DB::raw('now()');
            $post->save();
        }

        return response()->json($post);
    }
    public function ajax_post_delete() {
        $post_id = Input::get('post_id');

        $post = Post::find($post_id);
        
        $category = Category::find($post->category_id);
        $category->posts_num -= 1;
        $category->save();        
        
        $post->delete();

        return response()->json(['status' => '1']);
    }    

    public function ajax_options_save() {
        $options = array_except(Input::all(), '_token' );

        foreach ($options as $key=>$val) {
            $option = Option::firstOrCreate( ['name' => $key]);
            $option->value = $val;
            $option->save();

            $ret[] = $option;
        }

        return response()->json($ret);
    }

    public function ajax_category_create() {
        $category_name = Input::get('category_name');

        if (strlen($category_name) == 0) {
            return response()->json(['status' => 'error', 'error' => 'Categories cannot have empty names.']);
        }

        $category = Category::whereName($category_name)->first();
        if ($category) {
            return response()->json(['status' => 'error', 'error' => 'This category already exists.']);
        }

        $category = Category::create(['name' => $category_name, 'slug' => str_slug($category_name, '-')]);
        return response()->json(['status' => 'success', 'object' => $category]);
    }    
    
    ///////////////////////////////////////////////////////////////
    // Apis
    ///////////////////////////////////////////////////////////////
    public function api_getUser() {
        $input = Input::all();
        $uid = $input['uid'];

        $user = array();
        $users = DB::select('select * from users where id=?', [$uid]);
        if (!empty($users)) {
            $user = $users[0];
        }
        return Response::json([
            'user' => $user
        ]);
    }

    public function api_addUser() {
        $input = Input::all();

        $email = $input['email'];
        $password = $input['password'];
        $first_name = $input['first_name'];
        $last_name = $input['last_name'];
        $street = $input['street'];
        $city = $input['city'];
        $state = $input['state'];
        $zipcode = $input['zipcode'];
        $activated = '1';
        $mon_weight = 0;

        try {
            // Create the user
            $user = Sentinel::registerAndActivate(array('email' => $email
                        , 'password' => $password
                        , 'first_name' => $first_name
                        , 'last_name' => $last_name
                        , 'street' => $street
                        , 'city' => $city
                        , 'state' => $state
                        , 'zipcode' => $zipcode
                        , 'mon_weight' => $mon_weight));

            // Find the group using the group id
            $userGroup = Sentinel::findRoleByName('User');
            // Assign the group to the user
            //$user->addGroup($userGroup);
            $userGroup->users()->attach($user);

            $status = 1;
        } catch (Exception $e) {
            $status = 0;
        }

        return Response::json([
                    'status' => $status,
                    'user' => $user
        ]);
    }

    public function api_editUser() {
        $input = Input::all();

        $uid = $input['uid'];
        $email = $input['email'];
        $password = $input['password'];
        $first_name = $input['first_name'];
        $last_name = $input['last_name'];
        $street = $input['street'];
        $city = $input['city'];
        $state = $input['state'];
        $zipcode = $input['zipcode'];
        $oldpassword = $input['oldpassword'];
        $oldemailaddr = $input['oldemailaddr'];

        if ( $oldemailaddr != $email ) {
            $existUser = User::where('email', '=', $email)->first();
            if ( $existUser ){
                return Response::json([
                    'status' => 0
                ]);
            }
            
        }

        $user = User::find($uid);

        $user->email = $email;
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->street = $street;
        $user->city = $city;
        $user->state = $state;
        $user->zipcode = $zipcode;

        $result = $user->save();

        if ($oldpassword != $password) {
            $user = Sentinel::findById($user->id);
            //$resetCode = $user->getResetPasswordCode();
            Sentinel::update($user, array('password' => $password));
//            $resetCode = $user->getResetPasswordCode();
//            $user->attemptResetPassword($resetCode, $password);
        }

        return Response::json([
            'status' => $result
        ]);
    }

    public function api_deleteUser() {
        $input = Input::all();
        $uid = $input['uid'];

        $rows = User::where('id', '=', $uid)->delete();
        $result = false;
        if ($rows > 0) {
            $result = true;
        }
        return Response::json([
            'status' => $result
        ]);
    }

    public function api_getMessage() {
        $input = Input::all();
        $mid = $input['mid'];

        $messages = DB::select('select n.* from message n where n.mid=?', [$mid]);
        if (!empty($messages)) {
            $message = $messages[0];
            $message->time = date('n-j-Y g:i A', $message->m_time);

            $users = DB::select('select u.first_name, u.last_name from message_user n inner join users u on n.uid = u.id where n.mid=?', [$mid]);
            $userStr = '';
            foreach ($users as $key => $item) {
                if ($key > 0)
                    $userStr = $userStr . ', ';

                $userStr = $userStr . $item->first_name . ' ' . $item->last_name;
            }

            $message->users = $userStr;
        }
        return Response::json([
            'msg' => $message
        ]);
    }

    public function api_deleteMessage() {
        $input = Input::all();
        $mid = $input['mid'];

        $rows = MessageModel::where('mid', '=', $mid)->delete();
        $result = false;
        if ($rows > 0) {
            $result = true;
        }
        return Response::json([
            'status' => $result
        ]);
    }

    public function api_getNotify() {
        $input = Input::all();
        $nid = $input['nid'];

        $message = array();
        $messages = DB::select('select n.* from notification n where n.nid=?', [$nid]);
        if (!empty($messages)) {
            $message = $messages[0];
            $message->time = date('n-j-Y g:i A', $message->n_time);

            $users = DB::select('select u.first_name, u.last_name from notification_user n inner join users u on n.uid = u.id where n.nid=?', [$nid]);
            $userStr = '';
            foreach ($users as $key => $item) {
                if ($key > 0)
                    $userStr = $userStr . ', ';

                $userStr = $userStr . $item->first_name . ' ' . $item->last_name;
            }

            $message->users = $userStr;
        }
        return Response::json([
            'msg' => $message
        ]);
    }

    public function api_deleteNotify() {
        $input = Input::all();
        $nid = $input['nid'];

        $rows = NotifyModel::where('nid', '=', $nid)->delete();
        $result = false;
        if ($rows > 0) {
            $result = true;
        }
        return Response::json([
            'status' => $result
        ]);
    }

    public function api_deleteOrder() {
        $input = Input::all();
        $oid = $input['oid'];

        $rows = Order::where('id', '=', $oid)->delete();
        $result = false;
        if ($rows > 0) {
            $result = true;
        }
        return Response::json([
            'status' => $result
        ]);
    }

    public function api_sendOrder() {
        $input = Input::all();
        $oid = $input['oid'];

        $order = Order::find($oid);
        $order->status = 3;
        $order->updatetime = time();
        $result = $order->save();

        $user = User::find($order->uid);
        if ($user->apncode != null && !empty($user->apncode)) {
            $deviceToken = $user->apncode;
            $passphrase = 'BurnVideo';
            $certfile = 'ckPro.pem';
            $notifyMessage = "Hello, " . $user->first_name . ". Burn Video has shipped your order via USPS. Order number here " . sprintf("A%'.08d", $oid);
            $this->sendAPNMessage($deviceToken, $passphrase, $certfile, $notifyMessage);
        }

        if ($user->gcmcode != null && !empty($user->gcmcode)) {
            $deviceToken = $user->gcmcode;
            $notifyMessage = "Hello, " . $user->first_name . ". Burn Video has shipped your order via USPS. Order number here " . sprintf("A%'.08d", $oid);
            $this->sendFCMMessage($deviceToken, $notifyMessage);
        }

        return Response::json([
            'status' => $result
        ]);
    }

    public function api_cancelOrder() {
        $input = Input::all();
        $oid = $input['oid'];

        $order = Order::find($oid);
        $order->status = 4;
        $order->updatetime = time();
        $result = $order->save();

        return Response::json([
            'status' => $result
        ]);
    }

    public function api_convertOrder() {
        $input = Input::all();
        $oid = $input['oid'];

        $order = Order::find($oid);
        $order->status = 2;
        $order->updatetime = time();
        $result = $order->save();

        return Response::json([
            'status' => $result
        ]);
    }

    public function api_updateSet() {
        $input = Input::all();

        $s_name = $input['s_name'];
        $data = $input['data'];

        $settingModel = Setting::where('s_name', $s_name)->first();
        if ( $settingModel ){
            $settingModel->s_value = $data;

        } else {
            
            $settingModel = new Setting();
            
            $settingModel->s_name = $s_name;
            $settingModel->s_value = $data;

        }
        $result = $settingModel->save();

        return Response::json([
            'status' => $result
        ]);
    }

    public function api_getShipArray() {
        $input = Input::all();
        $oid = $input['oid'];

        $order = Order::Find($oid);
        $order->orderid = sprintf("A%'.08d", $order->id);
        $order->inserttime = date("n-j-Y g:i A", $order->inserttime);
        $order->updatetime = date("n-j-Y g:i A", $order->updatetime);

        $user = User::find($order->uid);

        $shipping = DB::select('select n.* from order_shipping n where n.orderid=?', [$oid]);

        return Response::json([
            'user' => $user,
            'order' => $order,
            'shippings' => $shipping
        ]);
    }

    public function api_setOrderDvdTitle() {
        $input = Input::all();
        $oid = $input['oid'];
        $title = $input['title'];

        $order = Order::Find($oid);
        $order->dvdtitle = $title;
        $result = $order->save();

        if ($result) {
            return Response::json([
                'status' => 0
            ]);
        }

        return Response::json([
            'status' => 1
        ]);
    }

    public function api_searchUsers() {
        $input = Input::all();
        $search = isset($input['search'])?$input['search']:"";
        $state = isset($input['state'])?$input['state']:"";//adding part
        $city = isset($input['city'])?$input['city']:"";//adding part
		$additionalCond = "";
		if (isset($search) && !empty($search))
			$additionalCond .= "and concat( u.first_name, ' ', u.last_name) like '%" . $search . "%' ";

		if (isset($state) && !empty($state))
			$additionalCond .= "and u.state='".$state."' ";

		if (isset($city) && !empty($city))
			$additionalCond .= "and u.city='".$city."' ";

		$searchUser_sql = "select concat( u.first_name, ' ', u.last_name) as uname from users u "
                        . "inner join role_users ug on u.id = ug.user_id "
                        . "where ug.role_id = 2 "
                        .$additionalCond
                        . "order by u.id asc";
        $users = DB::select($searchUser_sql);

        return Response::json([
            'users' => $users
        ]);
    }
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function api_stateList() {

        $states = DB::select("select state from users u "
                        . "inner join role_users ug on u.id = ug.user_id "
                        . "where ug.role_id = 2 "
                        . "group by u.state "
                        . "order by u.state asc");

		return Response::json([
			'states' => $states
		]);
    }

    public function api_cityList() {
        $input = Input::all();
        $state = $input['state'];

        $cities = DB::select("select city from users u "
                        . "inner join role_users ug on u.id = ug.user_id "
                        . "where ug.role_id = 2 and u.state='" . $state . "' "
                        . "group by u.city "
                        . "order by u.city asc");

        return Response::json([
            'cities' => $cities
        ]);
    }

	public function api_mailImageUpload() {

		try {
			$allowed = array('png', 'jpg', 'gif');
			$rules = [
				'file' => 'required|image|mimes:jpeg,jpg,png,gif'
			];
			$data = Input::all();
			$validator = Validator::make($data, $rules);
			if ($validator->fails()) {
				return Response::json([
					'retCode' => self::ERR_INVALIDFILE,
					'msg' => self::MSG_INVALIDFILETYPE
				]);
			}

			$destinationPath = 'uploads/mailing/';
			if(!file_exists($destinationPath)) {
				mkdir($destinationPath);
			}

			if(Input::hasFile('file')){
				$extension = Input::file('file')->getClientOriginalExtension();
				if(!in_array(strtolower($extension), $allowed)){
					return Response::json([
						'status' => "Invalid File type"
					]);
				} else {
					$filename = uniqid() . '_mail.' . $extension;
					if (Input::file('file')->move($destinationPath, $filename)) {
						return Response::json([
							'retCode' => self::ERR_SUCCESS,
							'msg' => self::MSG_SUCCESS,
							'url' => url($destinationPath . $filename)
						]);
					}
				}
			} else {
				return Response::json([
					'retCode' => self::ERR_INVALIDFILE,
					'msg' => self::MSG_INVALIDFILETYPE
				]);
			}

		} catch (Exception $e) {
			
		}

		return Response::json([
			'retCode' => self::ERR_INVALIDFILE,
			'msg' => self::MSG_INVALIDFILETYPE
		]);

	}
  
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
    /**
     * Send Push APN
     * @param $deviceToke  ex: 374822eb9f92e85c09ff5c985e57e4a5e8095a8fe1b1765f18398a4a76b39b01
     * @param $passpharse  ex: BurnVideo
     * @param $certfile    ex: ck.pem
     */
    private function sendAPNMessage($deviceToken, $passphrase, $certfile, $message) {
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $certfile);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
		// sandbox mode
        //$fp = stream_socket_client(
        //        'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
		//producton mode
        $fp = stream_socket_client(
                'ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp)
            return -1;

        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'sound' => 'default'
        );

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        // Close the connection to the server
        fclose($fp);

        return 0;
    }

    private function sendFCMMessage($deviceToken, $message) {
        
        $fields = array(
            'registration_ids' => array($deviceToken),
            'data' => array('message' => $message),
            'content-available'  => true,
            'priority' => 'high',
            'notification' => array("title" => "Burn Video", 'body' => $message, 'sound' => 'default')
        );
        
        $url = 'https://fcm.googleapis.com/fcm/send';
 
        //building headers for the request
        $headers = array(
            'Authorization: key=' . self::FIREBASE_API_KEY,
            'Content-Type: application/json'
        );
 
        //Initializing curl to open a connection
        $ch = curl_init();
 
        //Setting the curl url
        curl_setopt($ch, CURLOPT_URL, $url);
        
        //setting the method as post
        curl_setopt($ch, CURLOPT_POST, true);
 
        //adding headers 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        //disabling ssl support
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        //adding the fields in json format 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        //finally executing the curl request 
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
 
        //Now close the connection
        curl_close($ch);
 
        //and return the result 
        //return $result;

        return 0;
    }


    public function api_sendNotify() {
        $input = Input::all();
        $users = explode(",", $input['users']);
        $notifyMessage = $input['message'];

        $isSelectUser = false;
        foreach ($users as $uname) {
            $sendusers = DB::select("select id, email, first_name, last_name, apncode, gcmcode from users where concat(first_name, ' ', last_name) = '" . $uname . "'");
            foreach ($sendusers as $senduser) {
				if (($senduser->apncode != null && !empty($senduser->apncode)) || $senduser->gcmcode != null && !empty($senduser->gcmcode)) {
                    $isSelectUser = true;
                    break;
                }
            }
        }

        if ($isSelectUser == false) {
            return Response::json([
                'status' => false
            ]);
        }

        $notify = new NotifyModel;
        $notify->n_title = "";
        $notify->n_message = $notifyMessage;
        $notify->n_time = time();
        $notify->save();


        foreach ($users as $uname) {
            $sendusers = DB::select("select id, email, first_name, last_name, apncode, gcmcode from users where concat(first_name, ' ', last_name) = '" . $uname . "'");

            foreach ($sendusers as $senduser) {

				if (($senduser->apncode != null && !empty($senduser->apncode)) || $senduser->gcmcode != null && !empty($senduser->gcmcode)) {
					$notifyUser = new NotifyUserModel;
					$notifyUser->nid = $notify->id;
					$notifyUser->uid = $senduser->id;
					$notifyUser->sendflag = 0;
					$notifyUser->save();
				}
				/*
                if ($senduser->apncode != null && !empty($senduser->apncode)) {
                    $deviceToken = $senduser->apncode;
                    $passphrase = 'BurnVideo';
                    $certfile = 'ckPro.pem';
                    $this->sendAPNMessage($deviceToken, $passphrase, $certfile, $notifyMessage);
                }
                if ($senduser->gcmcode != null && !empty($senduser->gcmcode)) {
                    $deviceToken = $senduser->gcmcode;
                    //echo $deviceToken;
                    $this->sendFCMMessage($deviceToken, $notifyMessage);
                }*/
                
            }
        }

        return Response::json([
            'status' => true
        ]);
    }

    public function api_sendNotifyById() {
        $input = Input::all();
        $usersFrom = $input['usersfrom'];
        $usersEnd = $input['usersend'];
        $notifyMessage = $input['message'];

        $notify = new NotifyModel;
        $notify->n_title = "";
        $notify->n_message = $notifyMessage;
        $notify->n_sendtype = 1;
        $notify->n_time = time();
        $notify->save();

        $sendusers = DB::select("select id, apncode, gcmcode from users where id >= '".$usersFrom."' and id <= '".$usersEnd."'");
 
		foreach ($sendusers as $senduser) {


			if (($senduser->apncode != null && !empty($senduser->apncode)) || $senduser->gcmcode != null && !empty($senduser->gcmcode)) {

				$notifyUser = new NotifyUserModel;
				$notifyUser->nid = $notify->id;
				$notifyUser->uid = $senduser->id;
				$notifyUser->sendflag = 0;
				$notifyUser->save();

			}
			
		}

        return Response::json([
            'status' => true
        ]);
    }

    public function api_sendMail() {
        $input = Input::all();
        $subject = $input['title'];
        $sender = $input['sender'];
        $data = array('contents' => $input['message']);
        $users = explode(",", $input['users']);
        
        $message = new MessageModel;
        $message->m_title = $subject;
        $message->m_message = $input['message'];
        $message->m_sender = $sender;
        $message->m_time = time();
        $mresult = $message->save();

        foreach ($users as $uname) {
            //$sendusers = DB::select("select id, email, first_name, last_name from users where concat(first_name, ' ', last_name) = '" . trim($uname) . "'");
	        $sendusers = DB::select("select id, email, first_name, last_name from users where replace(concat(first_name, ' ', last_name), '\'', '') like '%" . trim($uname) . "%'");          
            
            foreach ($sendusers as $senduser) {
                if (filter_var($senduser->email, FILTER_VALIDATE_EMAIL)) {
                    $mu = new MessageUserModel;
                    $mu->mid = $message->id;
                    $mu->uid = $senduser->id;
                    $mu->save();

                    //Mail::send('emails.nonview', $data, function($mail) use ($sender, $senduser, $subject, $mu) {
                    //    $mail->from($sender);
                    //    $mail->to($senduser->email, $senduser->first_name . ' ' . $senduser->last_name)->subject($subject);
                      
						//$mu->sendflag = 1;
						//$mu->save();                      
                    //});
                }

                //break;
            }
        }

        return Response::json([
                    'status' => true
        ]);
    }

    public function api_sendMailById() {
        $input = Input::all();
        $subject = $input['title'];
        $sender = $input['sender'];
        $data = array('contents' => $input['message']);
        $usersFrom = $input['usersfrom'];
        $usersEnd = $input['usersend'];
        
        $message = new MessageModel;
        $message->m_title = $subject;
        $message->m_message = $input['message'];
        $message->m_sender = $sender;
        $message->m_sendtype = 1;
        $message->m_time = time();
        $mresult = $message->save();

        //foreach ($users as $uname) {
            //$sendusers = DB::select("select id, email, first_name, last_name from users where concat(first_name, ' ', last_name) = '" . trim($uname) . "'");
        $sendusers = DB::select("select id, email, first_name, last_name from users where id >= '".$usersFrom."' and id <= '".$usersEnd."'");          
            
        foreach ($sendusers as $senduser) {
            if (filter_var($senduser->email, FILTER_VALIDATE_EMAIL)) {
                $mu = new MessageUserModel;
                $mu->mid = $message->id;
                $mu->uid = $senduser->id;
                $mu->save();

                //Mail::send('emails.nonview', $data, function($mail) use ($sender, $senduser, $subject, $mu) {
                //    $mail->from($sender);
                //    $mail->to($senduser->email, $senduser->first_name . ' ' . $senduser->last_name)->subject($subject);
                  
                    //$mu->sendflag = 1;
                    //$mu->save();                      
                //});
            }

            //break;
        }
        //}

        return Response::json([
            'status' => true
        ]);
    }

    public function api_sendMailByIds() {
        $input = Input::all();
        $subject = $input['title'];
        $sender = $input['sender'];
        $data = array('contents' => $input['message']);
        $usersIds = rtrim($input['ids'], ',');
        
        $message = new MessageModel;
        $message->m_title = $subject;
        $message->m_message = $input['message'];
        $message->m_sender = $sender;
        $message->m_sendtype = 1;
        $message->m_time = time();
        $mresult = $message->save();

        //foreach ($users as $uname) {
            //$sendusers = DB::select("select id, email, first_name, last_name from users where concat(first_name, ' ', last_name) = '" . trim($uname) . "'");
        $sendusers = DB::select("select id, email, first_name, last_name from users where id in (".$usersIds.");");
            
        foreach ($sendusers as $senduser) {
            if (filter_var($senduser->email, FILTER_VALIDATE_EMAIL)) {
                $mu = new MessageUserModel;
                $mu->mid = $message->id;
                $mu->uid = $senduser->id;
                $mu->m_sendtype = 2;
                $mu->save();

                //Mail::send('emails.nonview', $data, function($mail) use ($sender, $senduser, $subject, $mu) {
                //    $mail->from($sender);
                //    $mail->to($senduser->email, $senduser->first_name . ' ' . $senduser->last_name)->subject($subject);
                  
                    //$mu->sendflag = 1;
                    //$mu->save();                      
                //});
            }

            //break;
        }
        //}

        return Response::json([
            'status' => true
        ]);
    }
    
    
    public function api_addCalendarEvent() {
        $input = Input::all();
        $user = Sentinel::getUser();

        $title = $input['title'];
        $start = $input['start'];
        $end = $input['end'];

        $events = DB::select("select * from calendar_event "
                        . " where uid = " . $user->id
                        . " and title = '" . $title . "' "
                        . " and start = '" . $start . "'"
                        . " and end = '" . $end . "'");

        if (empty($events)) {
            $event = new CalendarEventModel;
            $event->uid = $user->id;
            $event->title = $title;
            $event->start = $start;
            $event->end = $end;
            $result = $event->save();

            if ($result) {
                return Response::json([
                            'status' => 0
                ]);
            } else {
                return Response::json([
                            'status' => 2
                ]);
            }
        }
        return Response::json([
                    'status' => 1
        ]);
    }

    public function api_deleteCalendarEvent() {
        $input = Input::all();
        $user = Sentinel::getUser();

        $title = $input['title'];
        $start = $input['start'];
        $end = $input['end'];

        $result = CalendarEventModel::where('title', '=', $title)
                ->where('start', '=', $start)
                ->where('end', '=', $end)
                ->where('uid', '=', $user->id)
                ->delete();

        if ($result) {
            return Response::json([
                        'status' => 0
            ]);
        }

        return Response::json([
                    'status' => 1
        ]);
    }

    public function api_editAdmin() {
        $curuser = Sentinel::getUser();
        $input = Input::all();
        $file = Input::file('admin_image');

        $uid = $curuser->id;
        $email = $input['admin_email'];
        $password = $input['admin_password'];
        $first_name = $input['admin_first_name'];
        $last_name = $input['admin_last_name'];
        $oldpassword = $input['admin_oldpassword'];

        $user = User::find($uid);

        $user->email = $email;
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $result = $user->save();

        if ($oldpassword != $password) {
//            $resetCode = $user->getResetPasswordCode();
//            $user->attemptResetPassword($resetCode, $password);
            $user = Sentinel::findById($user->id);
            //$resetCode = $user->getResetPasswordCode();
            Sentinel::update($user, array('password' => $password));
        }

        if (!empty($file) && $file->isValid()) {
            $destinationPath = 'uploads/admin/avatar' . $uid;
            if (file_exists($destinationPath)) {
                unlink($destinationPath);
            }

            $file->move('uploads/admin/', 'avatar' . $uid);
        }


        return Response::json([
            'user' => $user
        ]);
    }

    public function promoCodeList() {

        $user = Sentinel::getUser();

        $promocodes = PromoCode::get()->toArray();

        return view('admin.promocodeList')
                        ->with('user', $user)
                        ->with('count', count($promocodes))
                        ->with('promos', $promocodes);
    }

    public function api_addPromoCode() {
        $input = Input::all();

        $validation = PromoCode::validate($input);
        if ($validation->fails()) {
            $message = $validation->messages()->first();
            return Response::json([
                        'status' => 0,
                        'user' => [],
                        'message' => $message
            ]);
        }
        $user = PromoCode::create($input);
        if ($user) {
            $message = 'Promocode Insert successfully';
            return Response::json([
                        'status' => 1,
                        'user' => $user,
                        'message' => $message
            ]);
        } else {
            $message = 'Error in inserting promocode';
            return Response::json([
                        'status' => 0,
                        'user' => [],
                        'message' => $message
            ]);
        }
    }

    public function api_getPromoCode() {
        $input = Input::all();
        $promocode = PromoCode::where('id', $input['id'])->first();
        if ($promocode) {
            $promocode = $promocode->toArray();
        }
        return Response::json([
            'promocode' => $promocode
        ]);
    }

    public function api_editPromoCode() {
        $input = Input::all();

        $validation = PromoCode::validateUpdate($input, $input['id']);
        if ($validation->fails()) {
            $message = $validation->messages()->first();
            return Response::json([
                'status' => 0,
                'message' => $message
            ]);
        }
        $promo = PromoCode::where('id', $input['id'])->update($input);
        if ($promo) {
            return Response::json([
                'status' => 1,
                'message' => 'Done'
            ]);
        } else {
            return Response::json([
                'status' => 0,
                'message' => 'Error in updating'
            ]);
        }
    }

}
