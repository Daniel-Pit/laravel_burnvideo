<?php

namespace burnvideo\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use DB;
use Mail;
use Log;

use burnvideo\Models\MessageUserModel;
use Exception;

class UsermailSend extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'usersend:mailsend';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send emails to Users.';

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

			$searchsend_sql = "select mu.muid, m.m_sender, m.m_title, m.m_message, u.first_name, u.last_name, u.email "
                        . "from message_user as mu, message as m, users as u "
                        . "where mu.sendflag = 0 and mu.mid = m.mid and u.id = mu.uid and u.recv_promomails = 1 "
                        . "order by u.id asc "
                        . "limit 500;";

			$userMails = DB::select($searchsend_sql);
						
			foreach( $userMails as $sendUserMails )
			{
                $muid = $sendUserMails->muid;
				$message = $sendUserMails->m_message;
				$subject = $sendUserMails->m_title;
				$sender = $sendUserMails->m_sender;

				$data = array('contents' => $message);

				Mail::send('emails.nonview', $data, function($mail) use ($sender, $sendUserMails, $subject) {
					$mail->from($sender);
					$mail->to($sendUserMails->email, ucfirst(strtolower($sendUserMails->first_name)) . ' ' . ucfirst(strtolower($sendUserMails->last_name)))->subject($subject);

				});
				
				$messageUser = MessageUserModel::where('muid', $muid);
				$messageUser->update(['sendflag' => 1]);

			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
			Log::info("exception:".$e->getMessage()."\n".$e->getTraceAsString());
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
