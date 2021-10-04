<?php

namespace burnvideo\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use DB;
use Log;

use burnvideo\Models\S3HistoryModel;
use Exception;

class s3bucketdelete extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 's3bucket:delete';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'burnvideo s3 bucket delete.';


	static $lock_file = null;
	static $lock_files = array();
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
		
		$lockAppId = self::multi_lockapp(1);
		
		$getOptionFromNumber = $this->argument('from');
		$getOptionToNumber = $this->argument('to');
		
		try {
			
			$startI=1;
          	$toNumber = 0;
			if(isset($getOptionFromNumber) && !empty($getOptionFromNumber) 
				&& isset($getOptionToNumber) && !empty($getOptionToNumber)
				&& $getOptionFromNumber > 1 && $getOptionToNumber > 1){
				$startI = $getOptionFromNumber;
				$toNumber = $getOptionToNumber;
			}
			else {

				$getDeleteCommands = DB::select('select * from s3history where status=0 order by id desc limit 1;');
				if(!empty($getDeleteCommands)){
                  foreach ($getDeleteCommands as $key => &$itemCommands) {

                      $startI = $itemCommands->from;
                      $toNumber = $itemCommands->to;
                  }

                  $currentCommandId = $itemCommands->id;
                  $currentCommand = S3HistoryModel::find($currentCommandId);
                  $currentCommand->status = 1;
                  $currentCommand->save();
                }
			}

			

			for($itemNumber=$startI;$itemNumber<=$toNumber;$itemNumber++) {

				// get movie
				$files = DB::select('select f.* from file f '
								. ' inner join order_file of on of.fid = f.id '
								. ' inner join orders o on o.id = of.oid '
								. ' where o.id = ' . $itemNumber);


				foreach ($files as $index => &$file) {
					
					$ftype = $file->ftype;
					$source = $file->furl;//fixed_20160920
					$targetfile = $file->ftsurl;//fixed_20160920
                  
					//echo "current delete a target file->".$targetfile." filesource->".$source." ftype=".$ftype."\n";					

					////////////////////////////////////////////////////////////////////////////////////////////////////////
					//s3 bucket url check
					if (strpos($source, 's3.amazonaws.com/burunvideo') !== false) {

						$targetfile = str_replace("https://s3.amazonaws.com/burunvideo/", "", $source);
						//AWS S3 Source url s3://burunvideo/dvdzip/%s/%s, $item->id, $targetfile
						echo sprintf("s3cmd del -f s3://burunvideo/%s", $targetfile)."\n";;
						shell_exec(sprintf("s3cmd del -f s3://burunvideo/%s", $targetfile));
					}
						
							

				}

				//AWS S3 Source url s3://burunvideo/dvdzip/%s, $item->id
				echo sprintf("s3cmd del -r -f s3://burunvideo/dvdzip/%s", $itemNumber)."\n";;
				shell_exec(sprintf("s3cmd del -r -f s3://burunvideo/dvdzip/%s", $itemNumber));
			}

			if (isset($currentCommandId) && $currentCommandId > 0){
				$currentCommand->status = 2;
				$currentCommand->save();
			}

			if(isset($lockAppId) && $lockAppId > 0){
				self::unlock_appId($lockAppId);
			}

		} catch (Exception $e) {

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
		return array(
			array('to'),
			array('from'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

	static function multi_lockapp($locklimit) {

		for ($lockcount = 1; $lockcount <= $locklimit;$lockcount ++){

			if($lockcount == 1){
				$lockfilepath = '/tmp/s3-delete.pid';
			}else{
				$lockfilepath = '/tmp/s3-delete'.$lockcount.'.pid';	
			}


			self::$lock_files[$lockcount-1] = fopen($lockfilepath, 'c');

			$got_lock = flock(self::$lock_files[$lockcount-1], LOCK_EX | LOCK_NB, $wouldblock);
			Log::info("lock status: got:".$got_lock. " block:".$wouldblock);
			if (!$got_lock && !$wouldblock) {
				throw new Exception(
					"Unexpected error opening or locking lock file. Perhaps you " .
					"don't  have permission to write to the lock file or its " .
					"containing directory?"
				);
			}
			else if (!$got_lock && $wouldblock && $lockcount == $locklimit) {
				throw new Exception("All instances are Full; terminating.\n");
			}
			else if (!$got_lock && $wouldblock) {
				echo ("Another instance is already running on S3 delete processId Number ".$lockcount."; terminating.\n");
              	Log::info("Another instance is already running on S3 delete processId Number AppId = ". $lockcount);
				continue;
			}

			// Lock acquired; let's write our PID to the lock file for the convenience
			// of humans who may wish to terminate the script.
			ftruncate(self::$lock_files[$lockcount-1], 0);
			fwrite(self::$lock_files[$lockcount-1], getmypid() . "\n");
			Log::info("locking S3 Delete AppId = ". $lockcount);
			return $lockcount;
		}

	}

	static function multi_unlockapp($locklimit) {

		for ($lockcount = 1; $lockcount <= $locklimit;$lockcount ++){

			//$lockfilepath = '/tmp/order-convert'.$lockcount.'.pid';
			Log::info("S3 deleting unlocking");
			// All done; we blank the PID file and explicitly release the lock 
			// (although this should be unnecessary) before terminating.
			ftruncate(self::$lock_files[$lockcount-1], 0);
			flock(self::$lock_files[$lockcount-1], LOCK_UN);
		}
	}
	static function unlock_appId($appId) {
		Log::info("unlocking s3 delete AppId = ". $appId);
	    // All done; we blank the PID file and explicitly release the lock 
	    // (although this should be unnecessary) before terminating.
	    ftruncate(self::$lock_files[$appId-1], 0);
	    flock(self::$lock_files[$appId-1], LOCK_UN);
	}
}
