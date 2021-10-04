<?php
namespace burnvideo\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use DB;
use Log;

use burnvideo\Models\FileModel;
use burnvideo\Models\Order;
use burnvideo\Models\OrderFile;
use Exception;

class BurnOneDownload extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'order:download-one';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'One DVD Files Download For Testing.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	public function handle()
	{
		$mypath = dirname(__FILE__);
		$web_absolute_path = config('services.appRootPath');
		
		$web_absolute_uploadpath = $web_absolute_path . "public/uploads/order/";
		$shellcmd = "/bin/bash";

		$orderid = $this->argument('orderid');

		try {
			echo("\n" . date('Y-m-d H:i:s', time()) . " File Converting && Make DVD...\n");
			//////////////////////////////////////////////////////////////////////////////
			$orders = DB::select('select o.* from orders o '
							. ' where o.id = ' . $orderid . ' order by o.inserttime asc ');

			foreach ($orders as $key => &$item) {
				// create dvd scripts
				$dvd_dir = $web_absolute_uploadpath . $item->id;
				if (!file_exists($dvd_dir))
					mkdir($dvd_dir);

				// get movie
				$files = DB::select('select DISTINCT f.furl as uniquefurl, f.* from file f '
								. ' inner join order_file of on of.fid = f.id '
								. ' inner join orders o on o.id = of.oid '
								. ' where o.id = ' . $item->id. ' order by f.file_index, f.id asc;');

                echo "order " . $item->id. "\n";

				foreach ($files as $index => &$file) {
					$ftype = $file->ftype;
					$source = $file->furl;//fixed_20160920
					$targetfile = $file->ftsurl;//fixed_20160920
                    echo $source."\n";
                    echo $targetfile."\n";
                    
					if ($ftype == "video") {

						////////////////////////////////////////////////////////////////////////////////////////////////////////
						//s3 bucket url check
						if (strpos($source, 's3.amazonaws.com/burunvideo') !== false) {
							$replace_str = $web_absolute_uploadpath.$item->id;
							$targetfile = str_replace("https://s3.amazonaws.com/burunvideo/", "", $source);
							$source = str_replace("https://s3.amazonaws.com/burunvideo", $replace_str, $source);
						}
						else{
							if (strpos($source, config('services.appRootPath').'public/') === false) {
								$rep_target = substr($source, strrpos($source, '/') + 1);
								$source = $web_absolute_uploadpath.$item->id.'/'.$rep_target;
							}

						}
						
						try{
							//file check 2
							if(!file_exists($source)){
								//AWS S3 Source url s3://burunvideo/dvdzip/%s/%s, $item->id, $targetfile
								echo sprintf("s3cmd get --continue --force s3://burunvideo/%s %s", $targetfile, $source);
								$result_output = shell_exec(sprintf("s3cmd get --continue --force s3://burunvideo/%s %s", $targetfile, $source));
							}
							//file size check
							if(filesize($source) == 0){
								unlink($source);
							}
							
							//file check 1
							if(!file_exists($source)){
								//AWS S3 Source url s3://burunvideo/dvdzip/%s/%s, $item->id, $targetfile
								$result_output = shell_exec(sprintf("s3cmd get --continue --force s3://burunvideo/dvdzip/%s/%s %s", $item->id, $targetfile, $source));
							}

							if(!file_exists($source)){
								echo ("file not downloaded order number :".$item->id."\n".$source);
								continue;
							}
							
							if((filesize($source) == 0)){
								echo ("file not downloaded size 0 order number :".$item->id."\n".$source);
								unlink($source);
								continue;
							}
	
							// file broken check
							// $brokencheck_cmd = "/usr/local/bin/ffprobe -v error " . $source . " 2>&1";
							// $brokencheck_cmd = "$(which ffprobe) -v error " . $source . " 2>&1";
							// $brokenfileCheckResult = shell_exec($brokencheck_cmd);
							// if(!empty($brokenfileCheckResult)){
							// 	echo "error occur ->". $brokenfileCheckResult;
							// 	//echo "canceled file -> ".$source;
							// 	unlink($source);
							// 	continue;
							// }                      
							////////////////////////////////////////////////////////////////////////////////////////////////////////
								
						} catch (Exception $s3error) {
							//fwrite($output, "error\n");
							echo ("video download exception:".$s3error->getMessage()."\n".$s3error->getTraceAsString());
							throw $s3error;
						}						

					} else if ($ftype == "image") {
						
						////////////////////////////////////////////////////////////////////////////////////////////////////////
						//s3 bucket url check
						if (strpos($source, 's3.amazonaws.com/burunvideo') !== false) {
							$replace_str = $web_absolute_uploadpath.$item->id;
							$targetfile = str_replace("https://s3.amazonaws.com/burunvideo/", "", $source);
							$source = str_replace("https://s3.amazonaws.com/burunvideo", $replace_str, $source);
						}
						else{
							if (strpos($source, config('services.appRootPath').'public/') === false) {
								$rep_target = substr($source, strrpos($source, '/') + 1);
								$source = $web_absolute_uploadpath.$item->id.'/'.$rep_target;
							}

						}                      


						try{
							//file check 2
							if(!file_exists($source)){
								//AWS S3 Source url s3://burunvideo/dvdzip/%s/%s, $item->id, $targetfile
								$result_output = shell_exec(sprintf("s3cmd get --continue --force s3://burunvideo/%s %s", $targetfile, $source));
							}
							//file size check
							if(filesize($source) == 0){
								unlink($source);
							}
							
							//file check 1
							if(!file_exists($source)){
								//AWS S3 Source url s3://burunvideo/dvdzip/%s/%s, $item->id, $targetfile
								$result_output = shell_exec(sprintf("s3cmd get --continue --force s3://burunvideo/dvdzip/%s/%s %s", $item->id, $targetfile, $source));
							}
							
							if(!file_exists($source)){
								echo ("file not downloaded order number :".$item->id."\n".$source);
								continue;
							}
							
							//file size check
							if(filesize($source) == 0){
								echo ("file not downloaded size 0 order number :".$item->id."\n".$source);
								unlink($source);
								continue;
							}
							////////////////////////////////////////////////////////////////////////////////////////////////////////
								
						} catch (Exception $s3error) {
							//fwrite($output, "error\n");
							echo ("image download exception:".$s3error->getMessage()."\n".$s3error->getTraceAsString());
							throw $s3error;
						}						

						


					}
					
				}

			}

		} catch (Exception $e) {
			//fwrite($output, "error\n");
			Log::info("unknown exception:".$e->getMessage()."\n".$e->getTraceAsString());
		}
		
		echo("\n" . date('Y-m-d H:i:s', time()) . " end download\n");

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments() {
		return array(
			array('orderid', InputArgument::REQUIRED, 'orderid required.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions() {
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}


