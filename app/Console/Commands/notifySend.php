<?php

namespace burnvideo\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use DB;
use Log;
use PushNotification;

use burnvideo\Models\NotifyUserModel;
use Exception;

class notifySend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usersend:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to Users.';

	const FIREBASE_API_KEY = 'AAAA0B0xmZc:APA91bGW9dhIvCxQeFTPl54u6bPgMvrXUp06RTgru6qnJiujrxLINkU4PaFcRER4cu9_p82iGbwr0HQiMi0WbCJYLl4epRot-tPrETFblWwCrYhxDmFhb7sjULD2LQF89xk7mZBTpu_H';
	//const FIREBASE_API_KEY = 'AIzaSyARjklTTBau5w2a0LzVH46Lx5SqfdQtFV4';
	const FIREBASE_SENDER_ID = '893842987415';
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

			$searchsend_sql = "select nu.nuid, n.n_title, n.n_message, u.first_name, u.last_name, u.apncode, u.gcmcode "
                        . "from notification_user as nu, notification as n, users as u "
                        . "where nu.sendflag = 0 and nu.nid = n.nid and u.id = nu.uid "
                        . "order by u.id asc "
                        . "limit 150;";

			$sendUsers = DB::select($searchsend_sql);
			$apnCertFile = dirname(__FILE__)."/ckPro.pem";
            $passphrase = 'BurnVideo';			

			foreach( $sendUsers as $sendUser )
			{
                $nuid = $sendUser->nuid;
                //$notifyTitle = $sendUser->n_title;
                //$notifyTitle = "Dear ".$sendUser->first_name;
                $notifyTitle = "";
				$notifyMessage = $sendUser->n_message;

                if ($sendUser->apncode != null && !empty($sendUser->apncode)) {
                    $deviceToken = $sendUser->apncode;

                    $this->sendAPNMessage($deviceToken, $passphrase, $apnCertFile, $notifyTitle, $notifyMessage);
                    // $this->sendNotification_ios($deviceToken, $notifyMessage);
                }
                if ($sendUser->gcmcode != null && !empty($sendUser->gcmcode)) {
                    $deviceToken = $sendUser->gcmcode;
                    //echo $deviceToken;
                    $this->sendFCMMessage($deviceToken, $notifyTitle, $notifyMessage);
                }

				$notifyUser = NotifyUserModel::where('nuid', $nuid);
				$notifyUser->update(['sendflag' => 1]);
				sleep(1);
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
			Log::info("push notifty send exception:".$e->getMessage()."\n".$e->getTraceAsString());
		}		

    }

    private function sendFCMMessage($deviceToken, $title, $message) {
        
        $fields = array(
            'registration_ids' => array($deviceToken),
            'data' => array('message' => $message),
            'content-available'  => true,
            'priority' => 'high',
            'notification' => array("title" => $title, 'body' => $message, 'sound' => 'default')
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

    private function sendAPNMessage($deviceToken, $passphrase, $certfile, $title, $message) {
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
		/*
        $body['aps'] = array(
            'alert' => $message,
            'sound' => 'default'
        );
		*/

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

    private function sendNotification_ios($deviceToken, $message) {
        PushNotification::app('BurnVideoIOS')
            ->to($deviceToken)
            ->send($message);

        return 0;
    }

}
