<?php
namespace burnvideo\Http\Controllers;

use burnvideo\Events\ConvertedOrderEvent;
use burnvideo\Events\CreatedOrderEvent;
use Illuminate\Support\Facades\DB;
use Sentinel;
use Braintree_Configuration;
use Braintree_ClientToken;
use Braintree_Customer;
use Braintree_Transaction;
use Braintree_PaymentMethod;
use Input;
use Validator;
use Mailgun;
use Response;
use Mail;
use Log;
use PushNotification;

use burnvideo\Models\PromoCode;
use burnvideo\Models\PromoCodeUse;
use burnvideo\Models\User;
use burnvideo\Models\FileModel;
use burnvideo\Models\Order;
use burnvideo\Models\OrderFile;
use burnvideo\Models\OrderShipping;
use burnvideo\Models\Transaction;
use burnvideo\Models\PaymentMethodUpdate;
use burnvideo\Models\NotifyModel;
use burnvideo\Models\NotifyUserModel;
use burnvideo\Models\MessageModel;
use burnvideo\Models\MessageUserModel;


class ApiController extends Controller {
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
    const ERR_NOORDERFILES = '-141';
	const ERR_RESETPASSWORDFAILED = '-150';
	const ERR_INVALIDPASSWORDCODE = '-151';

	/* error string */
	const MSG_SUCCESS = 'Success';
  	const MSG_SUCCESS_CHANGE = 'Change Success';
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
	const MSG_ERR_NOORDERFILES = 'No order files';
	const MSG_ERRRESETPASSWORDFAILED = 'Password reset failed.';
	const MSG_INVALIDPASSWORDCODE = 'The provided password reset code is Invalid.';
	const MSG_INVALIDFILETYPE = 'Invalid File type.';


	const FIREBASE_API_KEY = 'AAAA0B0xmZc:APA91bGW9dhIvCxQeFTPl54u6bPgMvrXUp06RTgru6qnJiujrxLINkU4PaFcRER4cu9_p82iGbwr0HQiMi0WbCJYLl4epRot-tPrETFblWwCrYhxDmFhb7sjULD2LQF89xk7mZBTpu_H';
	
	const FIREBASE_SENDER_ID = '893842987415';	
	
	// Checked
	public function Signin() {
//		try {
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
				return Response::json([
					'retCode' => self::ERR_FAILAUTH,
					'msg' => self::MSG_ERR_FAILAUTH
				]);
			}

			$user = Sentinel::authenticate($credentials, false);
			if ( $user ) {
				if (Sentinel::check()) {
					$userGroup = Sentinel::findRoleByName('User');

					if ($user->inRole($userGroup)) {
						session_start();
						$sessid = session_id();
						DB::table('users')->where('id', $user->id)->update([ 'token' => $sessid]);

						$resuser = User::find($user->id);
						return Response::json([
									'retCode' => self::ERR_SUCCESS,
									'msg' => self::MSG_SUCCESS,
									'token' => $sessid,
									'uid' => $user->id,
									'user' => $resuser
						]);
					} else {
						return Response::json([
							'retCode' => self::ERR_FAILAUTH,
							'msg' => self::MSG_ERR_FAILAUTH
						]);
					}
				}

			}
//		} catch (Cartalyst\Sentinel\Users\LoginRequiredException $e) {
//			return Response::json([
//						'retCode' => self::ERR_FAILAUTH,
//						'msg' => self::MSG_ERR_FAILAUTH
//			]);
//		} catch (Cartalyst\Sentinel\Users\PasswordRequiredException $e) {
//			return Response::json([
//						'retCode' => self::ERR_FAILAUTH,
//						'msg' => self::MSG_ERR_FAILAUTH
//			]);
//		} catch (Cartalyst\Sentinel\Users\WrongPasswordException $e) {
//			return Response::json([
//						'retCode' => self::ERR_FAILAUTH,
//						'msg' => self::MSG_ERR_FAILAUTH
//			]);
//		} catch (Cartalyst\Sentinel\Users\UserNotFoundException $e) {
//			return Response::json([
//						'retCode' => self::ERR_FAILAUTH,
//						'msg' => self::MSG_ERR_FAILAUTH
//			]);
//		} catch (Cartalyst\Sentinel\Users\UserNotActivatedException $e) {
//			return Response::json([
//						'retCode' => self::ERR_FAILAUTH,
//						'msg' => self::MSG_ERR_FAILAUTH
//			]);
//		} catch (Cartalyst\Sentinel\Throttling\UserSuspendedException $e) {
//			return Response::json([
//						'retCode' => self::ERR_FAILAUTH,
//						'msg' => self::MSG_ERR_FAILAUTH
//			]);
//		} catch (Cartalyst\Sentinel\Throttling\UserBannedException $e) {
//			return Response::json([
//						'retCode' => self::ERR_FAILAUTH,
//						'msg' => self::MSG_ERR_FAILAUTH
//			]);
//		} catch (Exception $e) {
//			
//		}

		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}

	// Checked
	public function Signup() {
		try {
			$email = Input::get('email');
			$password = Input::get('password');
			$first_name = Input::get('first_name');
			$last_name = Input::get('last_name');
			$street = Input::get('street');
			$city = Input::get('city');
			$state = Input::get('state');
			$zipcode = Input::get('zipcode');

			if (strlen($email) == 0 || strlen($email) > 50 || strlen($password) < 4 || strlen($first_name) == 0 || strlen($last_name) == 0 || strlen($street) == 0 || strlen($city) == 0 || strlen($state) == 0 || strlen($zipcode) == 0) {
				return Response::json([
							'retCode' => self::ERR_INVALIDPARAM,
							'msg' => self::MSG_ERR_INVALIDPARAM
				]);
			}

			$users = DB::select('select * from users where email=?', [$email]);
			if (!empty($users)) {
				return Response::json([
							'retCode' => self::ERR_EXISTMAIL,
							'msg' => self::MSG_ERR_EXISTMAIL
				]);
			}
			$credentials = [
				'email' => Input::get('email'),
				'password' => Input::get('password'),
				'activated' => true,
				'mon_weight' => 40,
				'first_name' => Input::get('first_name'),
				'last_name' => Input::get('last_name'),
				'street' => Input::get('street'),
				'city' => Input::get('city'),
				'state' => Input::get('state'),
				'zipcode' => Input::get('zipcode')
            ];
			// Create the user
			$user = Sentinel::registerAndActivate($credentials);

			// Find the group using the group id
			$userGroup = Sentinel::findRoleByName('User');

			// Assign the group to the user
			$userGroup->users()->attach($user);

			return $this->Signin();
		} catch (Cartalyst\Sentinel\Users\UserExistsException $e) {
			return Response::json([
						'retCode' => self::ERR_EXISTMAIL,
						'msg' => self::MSG_ERR_EXISTMAIL
			]);
		} catch (Exception $e) {
			
		}

		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}

	public function FindPassword() {
//		try {
			$input = Input::all();
			$email = $input['email'];


			$new_password = str_random(6);
			$user = User::where('email' , '=', Input::get('email'))->first();
			//$resetCode = $user->getResetPasswordCode();
            if ( $user ) {
				$userId = $user->id;
				
				$user = Sentinel::findById($userId);
				//$resetCode = $user->getResetPasswordCode();
				if ( Sentinel::update($user, array('password' => $new_password)) ){
					Mail::send('emails.forgot', array('new_password' => $new_password), function($message) {
                        $input = Input::all();
                        $email = $input['email'];
                        $message->from('info@burnvideo.net');
                        $message->to($email)->subject('Reset Password!');
                    });
                    return Response::json([
                                'retCode' => self::ERR_SUCCESS,
                                'msg' => self::MSG_SUCCESS
                    ]);
				} else {
					return Response::json([
                                'retCode' => self::ERR_RESETPASSWORDFAILED,
                                'msg' => self::MSG_ERRRESETPASSWORDFAILED
                    ]);
				}                
            }

		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}

	public function ModifyPassword() {
//		try {
			$input = Input::all();
			$userid = $input['uid'];
			$new_password = $input['password'];

			$user = Sentinel::findById($userid);
			//$resetCode = $user->getResetPasswordCode();
			
            if ( $user ){
                if ( Sentinel::update($user, array('password' => $new_password)) ){
                    return Response::json([
                        'retCode' => self::ERR_SUCCESS,
                        'msg' => self::MSG_SUCCESS
                    ]);                    
                } else {
                    return Response::json([
                        'retCode' => self::ERR_RESETPASSWORDFAILED,
                        'msg' => self::MSG_ERRRESETPASSWORDFAILED
                    ]);
                }                
            }
//			if ($user->checkResetPasswordCode($resetCode)) {
//
//				if ($user->attemptResetPassword($resetCode, $new_password)) {
//					return Response::json([
//								'retCode' => self::ERR_SUCCESS,
//								'msg' => self::MSG_SUCCESS
//					]);
//				} else {
//					return Response::json([
//								'retCode' => self::ERR_RESETPASSWORDFAILED,
//								'msg' => self::MSG_ERRRESETPASSWORDFAILED
//					]);
//				}
//			} else {
//				return Response::json([
//							'retCode' => self::ERR_INVALIDPASSWORDCODE,
//							'msg' => self::MSG_INVALIDPASSWORDCODE
//				]);
//			}
//		} catch (Exception $e) {
//			
//		}

		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}


	public function UpdateInfo() {
		try {
			$input = Input::all();
			$uid = $input['uid'];
			$token = $input['token'];

			$dbuser = User::find($uid);
			if ($dbuser->token != $token && $token != 'aaabbbcccddd') {
				return Response::json([
					'retCode' => self::ERR_FAILAUTH,
					'msg' => self::MSG_ERR_FAILAUTH
				]);
			}

			$first_name = $input['first_name'];
			$last_name = $input['last_name'];
			$street = $input['street'];
			$city = $input['city'];
			$state = $input['state'];
			$zipcode = $input['zipcode'];

			if (strlen($first_name) == 0 || strlen($last_name) == 0 || strlen($street) == 0 || strlen($city) == 0 || strlen($state) == 0 || strlen($zipcode) == 0) {
				return Response::json([
					'retCode' => self::ERR_INVALIDPARAM,
					'msg' => self::MSG_ERR_INVALIDPARAM
				]);
			}
			
			$dbuser->first_name = $first_name;
			$dbuser->last_name = $last_name;
			$dbuser->street = $street;
			$dbuser->city = $city;
			$dbuser->state = $state;
			$dbuser->zipcode = $zipcode;
			$result = $dbuser -> save();
			
			if ( $result ) {
				return Response::json([
					'retCode' => self::ERR_SUCCESS,
					'msg' => self::MSG_SUCCESS
				]);
				
			}
		} catch (Exception $e) {
            return Response::json([
                        'retCode' => self::ERR_UNKNOWN,
                        'msg' => $e->getMessage()
            ]);
		}
		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);		

	}
	
	public function ReceivePromoMail() {
		try {
			$input = Input::all();
			$uid = $input['uid'];
			$token = $input['token'];

			$dbuser = User::find($uid);
			if ($dbuser->token != $token && $token != 'aaabbbcccddd') {
				return Response::json([
					'retCode' => self::ERR_FAILAUTH,
					'msg' => self::MSG_ERR_FAILAUTH
				]);
			}

			$recvFlag = $input['recvFlag'];

			if (strlen($recvFlag) == 0) {
				return Response::json([
					'retCode' => self::ERR_INVALIDPARAM,
					'msg' => self::MSG_ERR_INVALIDPARAM
				]);
			}
			
			
			
			$dbuser->recv_promomails = $recvFlag;
			$result = $dbuser -> save();
			
			if ( $result ) {
				return Response::json([
					'retCode' => self::ERR_SUCCESS,
					'msg' => self::MSG_SUCCESS
				]);
				
			}
		} catch (Exception $e) {
            return Response::json([
                        'retCode' => self::ERR_UNKNOWN,
                        'msg' => $e->getMessage()
            ]);
		}
		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);		

	}


	// Checked
	public function GetUserInfo() {
		try {
			$input = Input::all();
			$uid = $input['uid'];
			$token = $input['token'];

			$dbuser = DB::table('users')->where('id', $uid)->first();
			if ($dbuser->token != $token && $token != 'aaabbbcccddd') {
				return Response::json([
							'retCode' => self::ERR_FAILAUTH,
							'msg' => self::MSG_ERR_FAILAUTH
				]);
			}

			$user = array();
			$users = DB::select('select * from users where id=?', [$uid]);
			if (!empty($users)) {
				$user = $users[0];
			}

			return Response::json([
						'retCode' => self::ERR_SUCCESS,
						'msg' => self::MSG_SUCCESS,
						'user' => $user
			]);
		} catch (Exception $e) {
			
		}
		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}

	// Checked
	public function GetOrderList() {
		try {
			$input = Input::all();
			$uid = $input['uid'];
			$token = $input['token'];
			//$first = $input['first'];
			//$count = $input['count'];

			$dbuser = DB::table('users')->where('id', $uid)->first();
			if ($dbuser->token != $token && $token != 'aaabbbcccddd') {
				return Response::json([
							'retCode' => self::ERR_FAILAUTH,
							'msg' => self::MSG_ERR_FAILAUTH
				]);
			}

			$orders = DB::select('select o.*, u.first_name, u.last_name from orders o '
							. ' left join users u on u.id = o.uid '
							. ' where o.uid = ' . $uid
							. ' order by o.inserttime desc;');

			foreach ($orders as $key => &$item) {
				$files = DB::select('select f.* from file f '
								. ' inner join order_file of on of.fid = f.id '
								. ' inner join orders o on o.id = of.oid '
								. ' where o.id = ' . $item->id);
				$item->files = $files;
				$item->inserttime = date('Y-m-d H:i:s', $item->inserttime);
				$item->updatetime = date('Y-m-d H:i:s', $item->updatetime);
			}

			return Response::json([
						'retCode' => self::ERR_SUCCESS,
						'msg' => self::MSG_SUCCESS,
						'data' => $orders
			]);
		} catch (Exception $e) {
			
		}
		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}
	
	public function GetOrderHistory() {
		try {
			$input = Input::all();
			$uid = $input['uid'];
			$token = $input['token'];
			//$first = $input['first'];
			//$count = $input['count'];

			$dbuser = DB::table('users')->where('id', $uid)->first();
			if ($dbuser->token != $token && $token != 'aaabbbcccddd') {
				return Response::json([
							'retCode' => self::ERR_FAILAUTH,
							'msg' => self::MSG_ERR_FAILAUTH
				]);
			}

			$orders = DB::select('select o.*, u.first_name, u.last_name from orders o '
							. ' left join users u on u.id = o.uid '
							. ' where o.uid = ' . $uid
							. ' order by o.inserttime desc;');

			foreach ($orders as $key => &$item) {
				$item->inserttime = date('Y-m-d H:i:s', $item->inserttime);
				$item->updatetime = date('Y-m-d H:i:s', $item->updatetime);
			}


			return Response::json([
						'retCode' => self::ERR_SUCCESS,
						'msg' => self::MSG_SUCCESS,
						'data' => $orders
			]);
		} catch (Exception $e) {
			
		}
		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}

	// Checked
	public function GetOrderOne() {
		try {
			$input = Input::all();
			$uid = $input['uid'];
			$token = $input['token'];
			$oid = $input['orderid'];
			//$first = $input['first'];
			//$count = $input['count'];

			$dbuser = DB::table('users')->where('id', $uid)->first();
			if ($dbuser->token != $token && $token != 'aaabbbcccddd') {
				return Response::json([
							'retCode' => self::ERR_FAILAUTH,
							'msg' => self::MSG_ERR_FAILAUTH
				]);
			}

			$orders = DB::select('select o.*, u.first_name, u.last_name from orders o '
							. ' left join users u on u.id = o.uid '
							. ' where o.uid = ' . $uid
							. ' and o.id = ' . $oid
							. ' order by o.inserttime desc;');

			foreach ($orders as $key => &$item) {
				$files = DB::select('select f.* from file f '
								. ' inner join order_file of on of.fid = f.id '
								. ' inner join orders o on o.id = of.oid '
								. ' where o.id = ' . $item->id);
				$item->files = $files;
				$item->inserttime = date('Y-m-d H:i:s', $item->inserttime);
				$item->updatetime = date('Y-m-d H:i:s', $item->updatetime);
			}

			return Response::json([
						'retCode' => self::ERR_SUCCESS,
						'msg' => self::MSG_SUCCESS,
						'data' => $orders
			]);
		} catch (Exception $e) {
			
		}
		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}

	// Checked
	public function GetOrderDetail() {
		try {
			$input = Input::all();

			$uid = $input['uid'];
			$token = $input['token'];
			$oid = $input['oid'];

			$dbuser = DB::table('users')->where('id', $uid)->first();
			if ($dbuser->token != $token && $token != 'aaabbbcccddd') {
				return Response::json([
							'retCode' => self::ERR_FAILAUTH,
							'msg' => self::MSG_ERR_FAILAUTH
				]);
			}

			$order = Order::find($oid);
			$order->inserttime = date('Y-m-d H:i:s', $order->inserttime);
			$order->updatetime = date('Y-m-d H:i:s', $order->updatetime);

			$files = DB::select('select f.* from file f '
							. ' inner join order_file of on of.fid = f.id '
							. ' inner join orders o on o.id = of.oid '
							. ' where o.id = ' . $order->id);

			return Response::json([
						'retCode' => self::ERR_SUCCESS,
						'msg' => self::MSG_SUCCESS,
						'order' => $order,
						'files' => $files
			]);
		} catch (Exception $e) {
			
		}
		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}

	// Checked
	public function GetSetting() {
		try {
			$input = Input::all();

			$uid = $input['uid'];
			$token = $input['token'];
			$key = $input['attribute'];

			//$dbuser = DB::table('users')->where('id', $uid)->first();
			//if ($dbuser->token != $token && $token != 'aaabbbcccddd') {
			//	return Response::json([
			//				'retCode' => self::ERR_FAILAUTH,
			//				'msg' => self::MSG_ERR_FAILAUTH
			//	]);
			//}

			$dataval = "";
			if ( $key == "term" ){
				
				$dataval_term = "";
				$dataval_policies = "";
				$term_result = DB::select('select s_value from settings s where s.s_name = "terms"');
				$policies_result = DB::select('select s_value from settings s where s.s_name = "policies"');

				if ( !empty($term_result) && !empty($policies_result) ){
					$dataval_term = $term_result[0]->s_value;
					$dataval_policies = $policies_result[0]->s_value;
					$dataval = $dataval_policies ."<br>". $dataval_term;
					
				} else {
					$result = DB::select('select s_value from settings s where s.s_name = "term"');
					if (!empty($result)) {
						$dataval = $result[0]->s_value;
					}
					
				}
				
			} else {
				$result = DB::select('select s_value from settings s where s.s_name = "' . $key . '"');
				if (!empty($result)) {
					$dataval = $result[0]->s_value;
				}
				
			}


			return Response::json([
						'retCode' => self::ERR_SUCCESS,
						'msg' => self::MSG_SUCCESS,
						'data' => $dataval
			]);
		} catch (Exception $e) {
            return Response::json([
                        'retCode' => self::ERR_UNKNOWN,
                        'msg' => $e->getMessage()
            ]);			
		}
		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}
	public function sendPushTest(){
		// return "success";
        // $pushCertAndKeyPemFile = 'ckPro.pem';
        // $message = PushNotification::Message("Test", array(
        //     'badge' => 1,
        //     'sound' => 'default',            
        //     'custom' => array('custom' => array(
        //         'alert' => "push notification"
        //     ))
		// ));
		
		

        // // Send notification to iOS app
        // $result = PushNotification::app(['environment' => 'development',
        //         'certificate' => public_path() .'/'.$pushCertAndKeyPemFile,
        //         'passPhrase'  => 'BurnVideo',
        //         'service'     => 'apns'])
        //         ->to('ecbc9c826d31c5e1062fc132060e06081a2a640c9397513ee008e743d391d7d5')
		// 		->send($message); 
				
		// return $result;
    }
	private function sendPush($userid, $vmessage) {
		// Push Notify Message
		$user = User::find($userid);

		if ($user != null && $user->apncode != null && !empty($user->apncode)) {
			$notifyMessage = $vmessage;

			$notify = new NotifyModel;
			$notify->n_title = "";
			$notify->n_message = $notifyMessage;
			$notify->n_time = time();
			$notify->save();

			$notifyUser = new NotifyUserModel;
			$notifyUser->nid = $notify->id;
			$notifyUser->uid = $userid;
			$notifyUser->save();

			$deviceToken = $user->apncode;
			$passphrase = 'BurnVideo';
			$certfile = 'ckPro.pem';
			$message = $notifyMessage;
			// param $deviceToken, $passphrase, $certfile, $message

			try {
				$ctx = stream_context_create();
				stream_context_set_option($ctx, 'ssl', 'local_cert', $certfile);
				stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

				// Open a connection to the APNS server
				// sandbox mode
				//$fp = stream_socket_client(
				//		'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
				// production mode
				$fp = stream_socket_client(
						'ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

				// $return "success";
				if (!$fp)
					return -1;

				// Create the payload body
				$body['aps'] = array(
					'alert' => $notifyMessage,
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
			} catch (Exception $ex) {
				
			}
		}
	}

	private function sendFCM($userid, $vmessage) {
		// Push Notify Message
		$user = User::find($userid);
		
		//android send
			
		if ($user != null && $user->gcmcode != null && !empty($user->gcmcode)) {
			$notifyMessage = $vmessage;

			$notify = new NotifyModel;
			$notify->n_title = "";
			$notify->n_message = $notifyMessage;
			$notify->n_time = time();
			$notify->save();

			$notifyUser = new NotifyUserModel;
			$notifyUser->nid = $notify->id;
			$notifyUser->uid = $userid;
			$notifyUser->save();

			$deviceToken = $user->gcmcode;
			$message = $notifyMessage;
			
	        $fields = array(
	            'registration_ids' => array($deviceToken),
	            'data' => array('message' => $message),
	            'content-available'  => true,
	            'priority' => 'high',
	            'notification' => array("title" => "Confirm Order", 'body' => $message, 'sound' => 'default')
	        );
	        
			// param $deviceToken, $passphrase, $certfile, $message

			try {

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

			} catch (Exception $ex) {
				//return $ex->getMessage();
			}			
		}

	}
	
	public function CreateOrder() {
		// Log::useFiles(storage_path() . '/logs/app.log');
		// Log::info("create order logs: \n".print_r(Input::all(), true));
		Log::info(print_r(Input::all(), true));
		try {
			$input = Input::all();

			$dvdtitleBuff = $input['dvdtitle'];
			if(isset($dvdtitleBuff) && !empty($dvdtitleBuff)){
				
				$dvdtitlBuffArray = explode(":", $dvdtitleBuff);
				$dvdtitleBuffArrayCount = count($dvdtitlBuffArray);
				
				if ( $dvdtitleBuffArrayCount > 1 ) {
					for($titlecount=0;$titlecount<$dvdtitleBuffArrayCount;$titlecount++){
						if($titlecount == 0){
							$dvdtitleBuff = $dvdtitlBuffArray[$titlecount];
						} else if(($titlecount + 1) == $dvdtitleBuffArrayCount){//last title
							
							if(!empty(trim($dvdtitlBuffArray[$titlecount]))){
								$dvdtitleBuff .= ":".$dvdtitlBuffArray[$titlecount];
							}else {
								$dvdtitleBuff .= "";
							}
	
						} else{
							$dvdtitleBuff .= ":".$dvdtitlBuffArray[$titlecount];
						}
					}
					
				}


			}else {
				$dvdtitleBuff = "";
			}          
          
			$uid = $input['uid'];
			$token = $input['token'];
			$filecount = $input['filecount'];
			$devicetype = $input['devicetype'];
			$weight = $input['slotcount'];
			$dvdtitle = $dvdtitleBuff;
			$dvdcount = $input['dvdcount'];
			$dvdcaption = $input['dvd_caption'];
			$preorderid = $input['preorderid'];
			$shipping = $input['shipping'];
			$files = $input['files'];
			$usedExtraSpaces = isset($input['extra_spaces'])?$input['extra_spaces']:false;

			$dbuser = User::find($uid);
			//$usertoken = $dbuser->token;
			if ($dbuser->token != $token && $token != 'aaabbbcccddd') {
				return Response::json([
					'retCode' => self::ERR_FAILAUTH,
					'msg' => self::MSG_ERR_FAILAUTH
				]);
			}

			/*
			  $userweight = $dbuser->mon_weight;
			  if( $userweight < intval($weight) )
			  {
			  return Response::json([
			  'retCode' => self::ERR_INSUFFICIENTWEIGHT,
			  'msg' => self::MSG_ERR_INSUFFICIENTWEIGHT
			  ]);
			  }
			 */

			if ($dbuser->customer_id == null) {
				return Response::json([
					'retCode' => self::ERR_NOPAYMENTSETTING,
					'msg' => self::MSG_ERR_NOPAYMENTSETTING
				]);
			}

            if ($filecount == 0) {
                return Response::json([
                    'retCode' => self::ERR_NOORDERFILES,
                    'msg' => self::MSG_ERR_NOORDERFILES,
                ]);
            }

			// Pay Process
			self::initBrainTree();

			// monthly check
			$transactions = DB::select("select * from `transaction` t where uid = " . $uid
							. " order by t.paytime desc limit 1");

			$pay_month = 0;
			if (empty($transactions)) {
				$pay_month = config('services.price-per-monthly');

				/*
				  $result = Braintree_Transaction::sale(
				  [
				  'customerId' => $dbuser->customer_id,
				  'amount' => $pay_month
				  ]
				  );
				  if(!$result->success) {
				  return Response::json([
				  'retCode' => self::ERR_FAILMONTHLYPAY,
				  'msg' => self::MSG_ERR_FAILMONTHLYPAY
				  ]);
				  }

				  $paytran = new Transaction;
				  $paytran->uid = $uid;
				  $paytran->oid = 0;
				  $paytran->ttype = 0;
				  $paytran->price = floatval($pay_month);
				  $paytran->paytime = time();
				  $paytran->save();
				 */
				$dbuser->mon_nextday = time() + 2592000;
				$dbuser->mon_weight = 40;
				$dbuser->mon_freedvd = 1;
				$dbuser->save();
			}

			if ($dbuser->first_ordertime == null || $dbuser->first_ordertime == 0) {
				$dbuser->first_ordertime = time();
				$dbuser->save();
			}

			$paydvdcount = $dvdcount;
			if ($dbuser->mon_freedvd == 1) {
				$dbuser->mon_freedvd = 0;
				$dbuser->save();
				$paydvdcount = $paydvdcount - 1;
			}

//			$pay_price = config('services.price-per-dvd') * intval($paydvdcount) + $pay_month;
			$pay_price = config('services.price-per-dvd') * intval($paydvdcount) + $pay_month;
			
			if ( $usedExtraSpaces && $weight > (config('services.spaces-per-dvd')) ){
				$pay_price = (config('services.price-per-dvd') + config('services.price-extra-space')) * intval($paydvdcount) + $pay_month;
			}

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

			if ($final_price <= 0) {
				return Response::json([
							'retCode' => self::ERR_FAILPAY,
							'msg' => self::MSG_ERR_FAILPAY
				]);
			}

			$final_price = number_format($final_price, 2);

			$result = Braintree_Transaction::sale(
							[
								'customerId' => $dbuser->customer_id,
								'amount' => $final_price
							]
			);
			if (!$result->success) {
				return Response::json([
							'retCode' => self::ERR_FAILPAY,
							'msg' => self::MSG_ERR_FAILPAY
				]);
			}

			// --------- create order -- begin --
			$time = date('YmdHis', time());
			$format = "OU%'.05d-%s";
			$orderTag = sprintf($format, $uid, $time);
			$result = 0;

			if ($preorderid == 0) {
				$order = new Order;
				$order->uid = $uid;
				$order->orderTag = $orderTag;
				$order->filecount = $filecount;
				$order->devicetype = $devicetype;
				$order->weight = $weight;
				$order->dvdtitle = $dvdtitle;
				$order->dvdcaption = $dvdcaption;
				$order->dvdcount = $dvdcount;
				$order->status = 0;
				$order->inserttime = time();
				$order->updatetime = time();
				$result = $order->save();

				$shipping_count = 0;
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
					
					if ( $shipping_count == 0 ) {
						$dbuser->street = $item['street'];
						$dbuser->city = $item['city'];
						$dbuser->state = $item['state'];
						$dbuser->zipcode = $item['zipcode'];
						$dbuser->save();
					}
					
					$shipping_count ++;
					
				}

				// add database to  files
				foreach ($files as $file) {
					$f = new FileModel;
					$f->uid = intval($uid);
					$f->ftype = $file['ftype'];
					$f->furl = $file['furl'];
					$f->ftsurl = $file['fname'];
					$f->fzipurl = '';
					$f->fplaytime = $file['fplaytime'];
					$f->fweight = $file['fweight'];
					$f->finserttime = time();
                  
					$findex = 0;
					if(isset($file['findex']) && !empty($file['findex'])){
						$findex = $file['findex'];
					}
					$f->file_index = $findex;
					
					$ct_captionText = "";
					if(isset($file['captiontext']) && !empty($file['captiontext'])){
						$ct_captionText = $file['captiontext'];
					}
					$f->ct_caption = $ct_captionText;
                  
					$f->save();

					$ofile = new OrderFile;
					$ofile->fid = $f->id;
					$ofile->oid = $order->id;
					$ofile->save();
				}

				$order->status = 1;
				$result = $order->save();

				if ($result) {
					$dbuser->mon_weight = $dbuser->mon_weight - intval($weight);
					$dbuser->save();
				}

				//$message = "Hello, " . $dbuser->first_name . ". Burn Video has received your order and all media files have been uploaded. Order " . sprintf("A%'.08d", $order->id);
				//$this->sendPush($uid, $message);

				//create promocode history
				if ($promo['id']) {
					$data = [
						'order_id' => $order->id,
						'promocode_id' => $promo['id'],
						'uid' => $uid
					];
					$promo_code = PromoCodeUse::create($data);
				}

				$paytran = new Transaction;
				$paytran->uid = $uid;
				$paytran->oid = $order->id;
				$paytran->ttype = 1;
				$paytran->price = floatval($pay_price);
				$paytran->promocode_id = $promo['id'];
				$paytran->final_price = floatval($final_price);
				$paytran->payorg = 0;
				$paytran->paytime = time();
				$paytran->save();
			} else {
				$oldOrder = Order::find($preorderid);

				$order = new Order;
				$order->uid = $oldOrder->uid;
				$order->orderTag = $orderTag;
				$order->filecount = $oldOrder->filecount;
				$order->weight = $oldOrder->weight;
				$order->zipurl = $oldOrder->zipurl;
				$order->devicetype = 1; //$devicetype;
				$order->dvdtitle = $dvdtitle;
				$order->dvdcaption = $dvdcaption;
				$order->inserttime = time();
				$order->updatetime = time();
				$order->status = 0;
				$order->preorder = $preorderid;
				$result = $order->save();

				$shipping_count = 0;
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
					
					if ( $shipping_count == 0 ) {
						$dbuser->street = $item['street'];
						$dbuser->city = $item['city'];
						$dbuser->state = $item['state'];
						$dbuser->zipcode = $item['zipcode'];
						$dbuser->save();
					}
					
					$shipping_count ++;
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

				//$message = "Hello, " . $dbuser->first_name . ". Burn Video has received your order and all media files have been uploaded. Order " . sprintf("A%'.08d", $order->id);
				//$this->sendPush($uid, $message);

				//$order->status  = 1;
				$order->status = 2;
				$result = $order->save();

				if ($promo['id']) {
					$data = [
						'order_id' => $order->id,
						'promocode_id' => $promo['id'],
						'uid' => $uid
					];
					$promo_code = PromoCodeUse::create($data);
				}

				$paytran = new Transaction;
				$paytran->uid = $uid;
				$paytran->oid = $order->id;
				$paytran->ttype = 1;
				$paytran->price = floatval($pay_price);
				$paytran->promocode_id = $promo['id'];
				$paytran->final_price = floatval($final_price);
				$paytran->payorg = 0;
				$paytran->paytime = time();
				$paytran->save();
			}
			///-------- create order - end ------------

			if ($result) {
                event(new CreatedOrderEvent((string)$order->id));

				return Response::json([
							'retCode' => self::ERR_SUCCESS,
							'msg' => self::MSG_SUCCESS,
							'orderid' => $order->id,
							'ordertag' => $orderTag
				]);
			}
		} catch (Exception $e) {
			return Response::json([
						'retCode' => self::ERR_UNKNOWN,
						'msg' => /* $e->getMessage().' trace' => $e->getTraceAsString() */ 'Shit Error'
			]);
		}

		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}

	public function ConfirmOrder() {
      	// Log::info(print_r(Input::all(), true));
		try {
			$input = Input::all();

			$uid = $input['uid'];
			$token = $input['token'];
			$orderid = $input['orderid'];

			$devicetype = 1;

			if( isset($input['devicetype']) && !empty($input['devicetype']) ){
				$devicetype = $input['devicetype'];
			}
			$usedExtraSpaces = isset($input['extra_spaces'])?$input['extra_spaces']:false;

			$dbuser = User::find($uid);
			//$dbuser = DB::table('users')->where('id', $uid)->first();
			
			if ($dbuser->token != $token && $token != 'aaabbbcccddd') {
		 		return Response::json([
							'retCode' => self::ERR_FAILAUTH,
							'msg' => self::MSG_ERR_FAILAUTH
				]);
			}

			$confirmOrderNumber = sprintf("A%'.08d", $orderid);
			$message = "Hello, " . $dbuser->first_name . ". Burn Video has received your order and all media files have been uploaded. Order " . $confirmOrderNumber;

			if($devicetype == 1 ){
				$this->sendPush($uid, $message);
			} else {
				$this->sendFCM($uid, $message);
			}

			// return Response::json([
			// 	'retCode' => 1,
			// 	'msg' => $result
			// ]);
			
			//mail confirm///

			//$mailMsg = "Hello, " . $dbuser->first_name . ". Burn Video has received your order and all media files have been uploaded successfully. Order " . sprintf("A%'.08d", $orderid);
			//$mailMsg .= " Please have your push notifications 'ON' for the app, as we send a message to your phone when your order ships USPS.";
			$mailReceiveUserName = ucfirst(strtolower($dbuser->first_name)) . ' ' . ucfirst(strtolower($dbuser->last_name));
			$mailReceiverEmail = $dbuser->email;
			
			$subject = "Burn Video Order #".$confirmOrderNumber." Confirmation";
			
			$orderBillingInfo = $mailReceiveUserName . "<br/>";
			$orderBillingInfo .= strip_tags($dbuser->street) . "  " . strip_tags($dbuser->city) . "<br/>";
			$orderBillingInfo .= strip_tags($dbuser->state) . " " . strip_tags($dbuser->zipcode) . "<br/>";

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
				
				$subPrice = number_format((float)$itemDvd * config('services.price-per-dvd'), 2, '.', '');
				
				if ( $usedExtraSpaces ){
					$subPrice = number_format((float)$itemDvd * (config('services.price-per-dvd') + config('services.price-extra-space')), 2, '.', '');
				}
				
				
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
            
			return Response::json([
						'retCode' => self::ERR_SUCCESS,
						'msg' => self::MSG_SUCCESS,
						'orderid' => $orderid
			]);
		} catch (Exception $e) {
			
		}
		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}

	// Checked
	public function SetAPNCode() {
		try {
			$input = Input::all();

			$uid = $input['uid'];
			$token = $input['token'];
			$code = $input['code'];

			$dbuser = DB::table('users')->where('id', $uid)->first();
			if ($dbuser->token != $token && $token != 'aaabbbcccddd') {
				return Response::json([
					'retCode' => self::ERR_FAILAUTH,
					'msg' => self::MSG_ERR_FAILAUTH
				]);
			}

			$user = User::find($uid);
			$user->apncode = $code;
			$result = $user->save();

			if ($result) {
				return Response::json([
							'retCode' => self::ERR_SUCCESS,
							'msg' => self::MSG_SUCCESS
				]);
			}
		} catch (Exception $e) {
			
		}
		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}

	public function SetGCMCode() {
		try {
			$input = Input::all();

			$uid = $input['uid'];
			$token = $input['token'];
			$code = $input['code'];

			$dbuser = DB::table('users')->where('id', $uid)->first();
			if ($dbuser->token != $token && $token != 'aaabbbcccddd') {
				return Response::json([
							'retCode' => self::ERR_FAILAUTH,
							'msg' => self::MSG_ERR_FAILAUTH
				]);
			}

			$user = User::find($uid);
			$user->gcmcode = $code;
			$result = $user->save();

			if ($result) {
				return Response::json([
							'retCode' => self::ERR_SUCCESS,
							'msg' => self::MSG_SUCCESS
				]);
			}
		} catch (Exception $e) {
			
		}
		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}

	public function GetBTClientToken() {
		try {
			$input = Input::all();

			$uid = $input['uid'];
			$token = $input['token'];

			$dbuser = DB::table('users')->where('id', $uid)->first();
			if ($dbuser->token != $token && $token != 'aaabbbcccddd') {
				return Response::json([
							'retCode' => self::ERR_FAILAUTH,
							'msg' => self::MSG_ERR_FAILAUTH
				]);
			}

			self::initBrainTree();

			if (!empty($dbuser->customer_id)) {

				$clientToken = Braintree_ClientToken::generate(array("customerId" => $dbuser->customer_id));

			} else {

				$clientToken = Braintree_ClientToken::generate();

			}

			return Response::json([
						'retCode' => self::ERR_SUCCESS,
						'msg' => self::MSG_SUCCESS,
						'btClientToken' => $clientToken
			]);
		}catch (Braintree_Exception_NotFound $e) {

			try{
				$clientToken = Braintree_ClientToken::generate();
				return Response::json([
							'retCode' => self::ERR_SUCCESS,
							'msg' => self::MSG_SUCCESS,
							'btClientToken' => $clientToken
				]);
			}
			catch (Exception $e){
				return Response::json([
						'retCode' => self::ERR_UNKNOWN,
						'msg' => $e->getMessage()
					]);	
			}

		} catch (Exception $e) {
			return Response::json([
						'retCode' => self::ERR_UNKNOWN,
						'msg' => $e->getMessage()
					]);			
		}
		return Response::json([
			'retCode' => self::ERR_UNKNOWN,
			'msg' => self::MSG_ERR_UNKNOWN
		]);
	}

	public function SetPaymentAccount() {
		try {
			$input = Input::all();

			$uid = $input['uid'];
			$clienttoken = $input['token'];
			$noncenumber = $input['noncenumber'];
			$methodtype = isset($input['methodtype'])?$input['methodtype']:"";

			$dbuser = DB::table('users')->where('id', $uid)->first();
			if ($dbuser->token != $clienttoken && $clienttoken != 'aaabbbcccddd') {
				return Response::json([
							'retCode' => self::ERR_FAILAUTH,
							'msg' => self::MSG_ERR_FAILAUTH
				]);
			}

			$user = User::find($uid);

			self::initBrainTree();

			if ($user->customer_id) {
				$customer = Braintree_Customer::find($user->customer_id);

				//var_dump($customer);
				//die();

				switch($methodtype){
					case "card":

						if(isset($customer->creditCards[0]->token)){
							$cardTokenCount = count($customer->creditCards);
							if($cardTokenCount > 1 ){
								for($tokenCount = 1; $tokenCount<$cardTokenCount;$tokenCount++){
									$card_token = $customer->creditCards[$tokenCount]->token;
									Braintree_PaymentMethod::delete($card_token);

								}
							}
						}

						if(isset($customer->paypalAccounts[0]->token)){
							$paypalTokenCount = count($customer->paypalAccounts);
							if($paypalTokenCount > 0 ){
								for($tokenCount = 0; $tokenCount<$paypalTokenCount;$tokenCount++){
									$paypal_token = $customer->paypalAccounts[$tokenCount]->token;
									Braintree_PaymentMethod::delete($paypal_token);
								}
							}
						}


						break;
					case "paypal":

						if(isset($customer->creditCards[0]->token)){
							$cardTokenCount = count($customer->creditCards);
							if($cardTokenCount > 0 ){
								for($tokenCount = 0; $tokenCount<$cardTokenCount;$tokenCount++){
									$card_token = $customer->creditCards[$tokenCount]->token;
									Braintree_PaymentMethod::delete($card_token);

								}
							}
						}

						if(isset($customer->paypalAccounts[0]->token)){
							$paypalTokenCount = count($customer->paypalAccounts);
							if($paypalTokenCount > 1 ){
								for($tokenCount = 1; $tokenCount<$paypalTokenCount;$tokenCount++){
									$paypal_token = $customer->paypalAccounts[$tokenCount]->token;
									Braintree_PaymentMethod::delete($paypal_token);
								}
							}
						}


						break;
					case "other":

						if(isset($customer->creditCards[0]->token)){
							$cardTokenCount = count($customer->creditCards);
							if($cardTokenCount > 0 ){
								for($tokenCount = 0; $tokenCount<$cardTokenCount;$tokenCount++){
									$card_token = $customer->creditCards[$tokenCount]->token;
									Braintree_PaymentMethod::delete($card_token);

								}
							}
						}

						if(isset($customer->paypalAccounts[0]->token)){
							$paypalTokenCount = count($customer->paypalAccounts);
							if($paypalTokenCount > 0 ){
								for($tokenCount = 0; $tokenCount<$paypalTokenCount;$tokenCount++){
									$paypal_token = $customer->paypalAccounts[$tokenCount]->token;
									Braintree_PaymentMethod::delete($paypal_token);
								}
							}
						}


						break;
					default:
						if(isset($customer->creditCards[0]->token)){
							$cardTokenCount = count($customer->creditCards);
							if($cardTokenCount > 1 ){
								for($tokenCount = 1; $tokenCount<$cardTokenCount;$tokenCount++){
									$card_token = $customer->creditCards[$tokenCount]->token;
									Braintree_PaymentMethod::delete($card_token);

								}
							}
						}

						if(isset($customer->paypalAccounts[0]->token)){
							$paypalTokenCount = count($customer->paypalAccounts);
							if($paypalTokenCount > 1 ){
								for($tokenCount = 1; $tokenCount<$paypalTokenCount;$tokenCount++){
									$paypal_token = $customer->paypalAccounts[$tokenCount]->token;
									Braintree_PaymentMethod::delete($paypal_token);
								}
							}
						}


						break;
				}



				$result = Braintree_PaymentMethod::create([
							'customerId' => $user->customer_id,
							'paymentMethodNonce' => $noncenumber,
							'options' => [
								'makeDefault' => true,
								'failOnDuplicatePaymentMethod' => true
							]
				]);
				//var_dump($result);
				//die();
				$paymentmethodupdate = new PaymentMethodUpdate;
				$paymentmethodupdate->uid = $uid;
				$paymentmethodupdate->paymentmethodnonce = $noncenumber;
				$paymentmethodupdate->token = $clienttoken;
				$paymentmethodupdate->result = $result;
				$paymentmethodupdate->customer = $customer;
				$paymentmethodupdate->type = 1;
				$paymentmethodupdate->save();

				if ($result->success) {
					if ($result->paymentMethod->isDefault()) {
						return Response::json([
								'retCode' => self::ERR_SUCCESS,
								'msg' => self::MSG_SUCCESS_CHANGE,
								'customerid' => $user->customer_id
						]);
					}else{
						return Response::json([
								'retCode' => self::ERR_SUCCESS,
								'msg' => self::MSG_SUCCESS,
								'customerid' => $user->customer_id
						]);
					}

				} else {
					$err_msg = '';
					foreach ($result->errors->deepAll() AS $error) {
						$err_msg .= $error->code . ": " . $error->message . "\n";
					}
					if(empty($err_msg))
						$err_msg = $result->message;
					return Response::json([
								'retCode' => self::ERR_FAILBRAINTREE,
								'msg' => $err_msg,
								'customerid' => $user->customer_id
					]);
				}


			}   else {
				$result = Braintree_Customer::create([
							'firstName' => $user->first_name,
							'lastName' => $user->last_name,
							'email' => $user->email,
							'paymentMethodNonce' => $noncenumber
				]);

				if ($result->success) {
					$customer_id = $result->customer->id;
					$user->customer_id = $customer_id;
					$user->save();

					return Response::json([
								'retCode' => self::ERR_SUCCESS,
								'msg' => self::MSG_SUCCESS,
								'customerid' => $customer_id
					]);
				} else {
					$err_msg = '';
					foreach ($result->errors->deepAll() AS $error) {
						$err_msg .= $error->code . ": " . $error->message . "\n";
					}
					if(empty($err_msg))
						$err_msg = $result->message;
					return Response::json([
								'retCode' => self::ERR_FAILBRAINTREE,
								'msg' => $err_msg
					]);
				}
			}
		} catch (Exception $e) {
			if(empty($e->getMessage())){
					return Response::json([
								'retCode' => self::ERR_SUCCESS,
								'msg' => self::MSG_SUCCESS,
								'customerid' => $customer_id
					]);		
			}

			return Response::json([
						'retCode' => self::ERR_UNKNOWN,
						'msg' => $e->getMessage()
			]);			
		}
		return Response::json([
					'retCode' => self::ERR_UNKNOWN,
					'msg' => self::MSG_ERR_UNKNOWN
		]);
	}

	public function testAPI() {
		try {
            
//			$ch = curl_init();
//			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//			curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/Signin');
//			curl_setopt($ch, CURLOPT_POST, true);
//			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//			curl_setopt($ch, CURLOPT_POSTFIELDS, "email=admin3@gmail.com&password=admin1");
//			$result = curl_exec($ch);
//			curl_close($ch);
            
            return Response::json([
                'retCode' => self::ERR_SUCCESS,
                'msg' => "api route working!!!"
            ]);
//			return Response::json([
//						'retCode' => self::ERR_SUCCESS,
//						'msg' => self::MSG_SUCCESS
//			]);
		} catch (Exception $e) {
			
		}
		return Response::json([
			'retCode' => self::ERR_UNKNOWN,
			'msg' => self::MSG_ERR_UNKNOWN
		]);
	}

	private function initBrainTree() {

		Braintree_Configuration::environment(config('services.braintree.mode'));
		Braintree_Configuration::merchantId(config('services.braintree.merchant_id'));
		Braintree_Configuration::publicKey(config('services.braintree.public_key'));
		Braintree_Configuration::privateKey(config('services.braintree.private_key'));
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
		if ($finalVal) {
			return Response::json(['status' => 1, 'value' => $deductVal]);
		} else {
			return Response::json(['status' => 0, 'message' => 'Invalid Promocode']);
		}
	}
	
	// get download list api
	public function GetDownloadOrder() {
		
		$downloadOrder = DB::table('orders')
			->where('downloaded', 0)
			->where('status', 2)
			->first();

		$download_url = "";
		$download_order_id = 0;

		$current_download_time = time();
		if ( !empty($downloadOrder) ){
			$download_url = $downloadOrder->zipurl;
			$download_order_id = $downloadOrder->id;
			

			$downloadingOrder = Order::find($download_order_id);
			$downloadingOrder->downloaded = 1;
			$downloadingOrder->download_at = $current_download_time;
			$downloadingOrder->save();
			
		}

		$old_download_time = $current_download_time - 60 * 60 * 2;//before 2 hours
		Order::where('download_at', '<', $old_download_time)
			->where('downloaded', '=', 1)
			->update(['downloaded' => 0]);


		return Response::json([
			'zip_url' => $download_url,
			'order_id' => $download_order_id
		]);
	}

	// set downloaded result
	public function SetDownloadedOrder($id) {
		
		$downloadedOrder = Order::find($id);

		if ( !empty($downloadedOrder) ){
			
			$downloadedOrder->downloaded = 2;
			
			if ($downloadedOrder->save()){

				return Response::json([
					'success' => true,
				]);
				
			}
		}
		
		return Response::json([
			'success' => false,
		]);

	}

    // set AWS convert result
    // Method GET
    // Param
    // orderid : order ID
    // status : converted status
    //          value :
    //              0: fail, 1: success
    public function awsConvertOrder() {
        $orderId = Input::get('orderId');
        $status = Input::get('status');

        event(new ConvertedOrderEvent($orderId, $status));

        return Response::json([
            'success' => true,
        ]);
    }

    // set AWS convert result
    // Method GET
    // Param
    // orderid : order ID
    // status : converted status
    //          value :
    //              0: fail, 1: success
    public function awsSubmitOrder() {
        $orders = DB::select('select o.* from orders o '
            . ' where o.status = 1 and o.burn_lock = 0 order by o.inserttime asc limit 1;');

        foreach ($orders as $key => &$item) {
            event(new CreatedOrderEvent((string)$item->id));
        }

        return Response::json([
            'success' => true,
        ]);
    }

    // test AWS convert result
    // Method GET
    // Param
    // orderid : order ID
    public function awsTestConvertOrder() {
        $orderId = Input::get('orderId');

        event(new CreatedOrderEvent($orderId));

        return Response::json([
            'success' => true,
        ]);
    }


}
