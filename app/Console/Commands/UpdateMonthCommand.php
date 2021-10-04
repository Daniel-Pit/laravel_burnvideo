<?php

namespace burnvideo\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use DB;
use Mail;
use Braintree_Transaction;
use Response;


use burnvideo\Models\User;
use burnvideo\Models\Transaction;
use Exception;

class UpdateMonthCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'order:month';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update Command.';

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
            try
            {
                $users = DB::select('select max(paytime) as paytime, u.* from `transaction` t inner join users u on t.uid = u.id group by uid');
                
                foreach( $users as $user )
                {
                    // monthly daytime diff
                    $today = time();
                    $paytime = $user->mon_nextday;
                    
                    $diff = 0;
                    if( $paytime != null && $paytime > 0  )
                    {
                        $diff = $paytime - $today;
                    }
                    
                    if( ( (3 * 3600 * 24) < $diff ) && 
                        ( $diff < (5 * 3600 * 24) ) && 
                        ( $user->customer_id  != null ) )
                    {                        
                        $dbuser = User::find($uid); 
                        
                        $username = $dbuser->first_name ;
                        $limitdate = date('F d Y', $paytime);                        
                        $street = $dbuser->street;
                        $city = $dbuser->city;
                        $state = $dbuser->state;
                        $zipcode = $dbuser->zipcode;
                        
                        $subject = $username . ", please upload your photos for the next GrooveBook due on " . $limitdate;  
                        
                        $data =  array( 'username' => $username
                                , 'limitdate' => $limitdate
                                , 'street' =>$street
                                , 'city' =>$city
                                , 'state' =>$state
                                , 'zipcode' =>$zipcode );
                        
                        Mail::send('emails.monthly', $data, function($mail) use ($senduser, $subject) {
                            $mail->from( 'reminder@burnvideo.net' );
                            $mail->to( $senduser->email, $senduser->first_name . ' ' . $senduser->last_name )->subject($subject);
                        });    
                    }
                    else if( $diff > 0 && $user->customer_id  != null )
                    {
                        $dbuser = User::find($uid); 
                        
                        // pay monthly
                        if( empty($transactions ))
                        {
                            $pay_month = config('services.price-per-monthly');

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
                            $paytran->ttype = 1;
                            $paytran->price = floatval($pay_month);
                            $paytran->paytime = time();                
                            $paytran->save();
                            
                            $dbuser->mon_nextday = time() + 2592000;
                            $dbuser->mon_weight = 40;
                            $dbuser->mon_freedvd = 1;
                            $dbuser->save();
                        }
                    }
                }
            }
            catch (Exception $e)
            {
            }
        
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
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
