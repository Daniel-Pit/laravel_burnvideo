<?php

namespace burnvideo\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use DB;
use Mail;
use Log;
use burnvideo\Models\Order;
use burnvideo\Models\User;
use burnvideo\Models\Transaction;
use burnvideo\Models\PromoCode;
use burnvideo\Models\MessageUserModel;
use Exception;

class UsermailSendTest extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'usersend:mailsendtest';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Test to send email to Users.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		//
		$uid = $this->argument('uid');
		$orderid = $this->argument('orderid');
		
		try {

			$dbuser = User::find($uid);
			//$dbuser = DB::table('users')->where('id', $uid)->first();
			
			$confirmOrderNumber = sprintf("A%'.08d", $orderid);
			// $message = "Hello, " . $dbuser->first_name . ". Burn Video has received your order and all media files have been uploaded. Order " . $confirmOrderNumber;

			// if($devicetype == 1 ){
			// 	$this->sendPush($uid, $message);
			// } else {
			// 	$this->sendFCM($uid, $message);
			// }
			
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
            echo "mail sent";
		} catch (Exception $e) {
			echo $e->getMessage()."\n".$e->getTraceAsString();

			echo PHP_EOL;
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments() {
		return array(
			array('uid', InputArgument::REQUIRED, 'uid required.'),
			array('orderid', InputArgument::REQUIRED, 'orderid required.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
