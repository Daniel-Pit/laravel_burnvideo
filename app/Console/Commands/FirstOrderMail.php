<?php

namespace burnvideo\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use DB;
use Log;
use Mail;

use burnvideo\Models\User;
use Exception;

class FirstOrderMail extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'order:firstsendmail';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send an email to User plaecd first Order.';

	static $afterSendHours = 96;//hours
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
		try
		{
			$currenttime = time() - (self::$afterSendHours * 3600);
			//echo time();
          	//return;
			$searchUser_sql = "select u.email as email, u.id as uid, u.first_name as first_name, u.last_name as last_name from users u "
                        . "inner join role_users ug on u.id = ug.user_id "
                        . "where ug.role_id = 2 and u.first_ordermail = 0 and u.first_ordertime > 0 and u.first_ordertime < '".$currenttime."'"
                        . "order by u.id asc";
			$users = DB::select($searchUser_sql);
						
			foreach( $users as $senduser )
			{
                        
				$username = $senduser->first_name;
				$useremail = $senduser->email;
				$uid = $senduser->uid;
				//if ($useremail == "m_pirogov@aol.com" || $useremail == "hurl23@hotmail.com"){
					$subject = "Thank you for your order";  
					//echo $useremail;
					$data =  array( 'username' => $username );
					
					Mail::send('emails.firstorder', $data, function($firstmail) use ($senduser, $subject) {
						$firstmail->from( 'sharla@burnvideo.net' );
						$firstmail->to( $senduser->email, $senduser->first_name . ' ' . $senduser->last_name )->subject($subject);
					});    
					
					$firstuser = User::find($uid);
					$firstuser->first_ordermail = 1;
					$firstuser->save();
				//}
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
