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
use burnvideo\Models\OrderShipping;
use Exception;


class BurnCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'order:convert';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'DVD File Convert.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Add files and sub-directories in a folder to zip file. 
	 * @param string $folder 
	 * @param ZipArchive $zipFile 
	 * @param int $exclusiveLength Number of text to be exclusived from the file path. 
	 */
	private static function folderToZip($folder, &$zipFile, $exclusiveLength) {
		$handle = opendir($folder);
		while (false !== $f = readdir($handle)) {
			if ($f != '.' && $f != '..') {
				$filePath = "$folder/$f";
				// Remove prefix from file path before add to zip. 
				$localPath = substr($filePath, $exclusiveLength);
				if (is_file($filePath)) {
					$zipFile->addFile($filePath, $localPath);
				} elseif (is_dir($filePath)) {
					// Add sub-directory. 
					$zipFile->addEmptyDir($localPath);
					self::folderToZip($filePath, $zipFile, $exclusiveLength);
				}
			}
		}
		closedir($handle);
	}

	/**
	 * Zip a folder (include itself). 
	 * Usage: 
	 *   HZip::zipDir('/path/to/sourceDir', '/path/to/out.zip'); 
	 * 
	 * @param string $sourcePath Path of directory to be zip. 
	 * @param string $outZipPath Path of output zip file. 
	 */
	public static function zipDir($sourcePath, $outZipPath) {
		$pathInfo = pathInfo($sourcePath);
		$parentPath = $pathInfo['dirname'];
		$dirName = $pathInfo['basename'];

		$z = new ZipArchive();
		$z->open($outZipPath, ZIPARCHIVE::CREATE);
		$z->addEmptyDir($dirName);
		self::folderToZip($sourcePath, $z, strlen("$parentPath/"));
		$z->close();
	}

	/**
	 * Execute the console command.
	 * 
	 * 
	 * made sample shell script 
	 * --------------------------------------------
	 * #!/bin/bash
	 * 
	 * export PATH=$PATH:/usr/local/bin
	 * export LD_LIBRARY_PATH=$LD_LIBRARY_PATH:/usr/lib:/usr/local/lib
	 * 
	 * cd /var/www/html/dvdburner/public/dvdtest
	 * 
	 * ffmpeg -i file1.mp4 -aspect 4:3 -target pal-dvd dvd1.mpg
	 * ffmpeg -i file2.mp4 -aspect 4:3 -target pal-dvd dvd2.mpg
	 * ffmpeg -loglevel error -fflags +genpts -re -i https://s3.amazonaws.com/burunvideo/26771F7E-FAB1-401C-8DC0-923A954543E4-8518-000006D8ABA6A82E.m4v -lavfi "[0:v]scale=ih*16/9:-1,boxblur=luma_radius=min(h\,w)/20:luma_power=1:chroma_radius=min(cw\,ch)/20:chroma_power=1[bg];[bg][0:v]overlay=(W-w)/2:(H-h)/2,crop=h=iw*9/16" -aspect 16:9 -target pal-dvd dvd-51-623.mpeg

	  ffmpeg -loglevel error -fflags +genpts -re -i https://s3.amazonaws.com/burunvideo/1F9AC25B-4C6E-4794-94AC-152976891FC3-8500-000006D82E65D50E.m4v -lavfi "[0:v]scale=ih*16/9:-1,boxblur=luma_radius=min(h\,w)/20:luma_power=1:chroma_radius=min(cw\,ch)/20:chroma_power=1[bg];[bg][0:v]overlay=(W-w)/2:(H-h)/2,crop=h=iw*9/16" -aspect 16:9 -target pal-dvd dvd-51-621.mpeg

		For NTSC-FORMAT
	   ffmpeg -i input.avi -target ntsc-dvd output.avi

	   ffmpeg -i input.avi -filter:v setpts=25025/24000*PTS -vcodec libx264 -preset ultrafast -crf 14 -acodec libmp3lame -ab 224k -filter:a atempo=0.959040959040959 -f matroska -r 24000/1001 NTSC.VTS.mpeg

	 * 
	 * dvdauthor -o dvd -x dvd.xml
	 * --------------------------------------------
	 * 
	 * made sample dvd xml file 
	 * <dvdauthor>
	 *   <vmgm>
	 *   </vmgm>
	 *   <titleset>
	 *     <titles>
	 *       <pgc>
	 *          <vob file="dvd1.mpg" />
	 *          <vob file="dvd2.mpg" />
	 *       </pgc>
	 *     </titles>
	 *   </titleset>
	 * </dvdauthor>
	 * 
	 * @return mixed
	 */
	public function handle()
	{
		//self::lock_app();
		$lockNumCount = self::num_cpus();
		$lockAppId = self::multi_lockapp($lockNumCount);
		$instanceNumber = config('services.instanceNumber');
      
		$mypath = dirname(__FILE__);
		$web_absolute_path = config('services.appRootPath');
		//$web_absolute_path= "D:/10_Langfang/30_DVDBurner/trunk/WebServer/";
		$web_absolute_uploadpath = $web_absolute_path . "public/uploads/order/";
		$shellcmd = "/bin/bash";
		$debug_output_path = $web_absolute_path . "convert.log";


		$output = fopen($debug_output_path, "a+");
		try {
			fwrite($output, "\n" . date('Y-m-d H:i:s', time()) . " File Converting && Make DVD...\n");
			//Old remove part
			$oldConditionDate = date("Y-m-d H:i:s", strtotime('-2 month'));
			//$throwOrders = DB::select('select o.* from orders o where (o.status = "" or o.status = 4) and o.created_at < "'.$oldConditionDate.'" order by o.inserttime asc;');
          	$throwOrders = DB::select('select o.* from orders o where o.status = 4 and o.created_at < "'.$oldConditionDate.'" order by o.inserttime asc;');
			foreach($throwOrders as $key => &$olditem){
				shell_exec(sprintf("rm -rf %s", $web_absolute_uploadpath . $olditem->id));	
			}
			//////////////////////////////////////////////////////////////////////////////
			$orders = DB::select('select o.* from orders o '
							. ' where o.status = 1 and o.burn_lock = 0 order by o.inserttime asc limit 1;');

			foreach ($orders as $key => &$item) {
				// create dvd scripts
				Log::info("itemId : ".  $item->id);
				$dvd_dir = $web_absolute_uploadpath . $item->id;
				if (!file_exists($dvd_dir))
					mkdir($dvd_dir);

				$dvd_dir = $web_absolute_uploadpath . $item->id . "/dvd";
				if (!file_exists($dvd_dir))
					mkdir($dvd_dir);
              
				$currentOrder = Order::find($item->id);
				$currentOrder->burn_lock = 1;
				$currentOrder->burn_app_num = $instanceNumber;
				$currentOrder->save();

				$dvd_zip = $web_absolute_uploadpath . $item->id . "/dvd-" . $item->id . ".zip";
				$dvd_url = "/uploads/order/" . $item->id . "/dvd-" . $item->id . ".zip";

				//$s3_dir = "/mys3bucket/dvdzip/" . $item->id;
				$s3_url = "https://s3.amazonaws.com/burunvideo/dvdzip/" . $item->id . "/dvd-" . $item->id . ".zip";

				// get movie
				$files = DB::select('select DISTINCT f.furl as uniquefurl, f.* from file f '
								. ' inner join order_file of on of.fid = f.id '
								. ' inner join orders o on o.id = of.oid '
								. ' where o.id = ' . $item->id. ' order by f.file_index, f.id asc;');


				//$dvd_zip_orig = $web_absolute_uploadpath . $item->id . "/dvd-orig-" . $item->id . ".zip";

//					self::zipDir($dvd_dir, $dvd_zip_orig);
				//shell_exec("zip -r  " . $dvd_zip_orig . " " . $dvd_dir . "\n");
				// move to s3bucket
				//$movecmd = "mv -f " . $dvd_zip_orig . " " . $s3_dir;
				//shell_exec($movecmd);



				// make shell script
				$scriptfile = $web_absolute_uploadpath . $item->id . "/mkdvd.sh";
				$fpScriptfile = fopen($scriptfile, "w+");

				fwrite($fpScriptfile, "#!/bin/bash\n");
				fwrite($fpScriptfile, "set -x #echo commands\n");
				fwrite($fpScriptfile, "export PATH=\$PATH:/usr/local/bin\n");
				fwrite($fpScriptfile, "export VIDEO_FORMAT=NTSC\n");
				fwrite($fpScriptfile, "export LD_LIBRARY_PATH=\$LD_LIBRARY_PATH:/usr/lib:/usr/local/lib\n");
				fwrite($fpScriptfile, "cd " . $web_absolute_uploadpath . $item->id . "\n");
				fwrite($fpScriptfile, "\n");

				// make dvd xml script
				$dvdxml = $web_absolute_uploadpath . $item->id . "/dvd.xml";
				$fpXml = fopen($dvdxml, "w+");
				fwrite($fpXml, "<dvdauthor>");
				fwrite($fpXml, "<vmgm></vmgm>");
				fwrite($fpXml, "<titleset><titles><pgc>");

				// write shell entry & dvd xml entry
				// add start logo
				$source = config('services.appRootPath')."public/logo.jpg";
				$dest = "dvd-logo.mpeg";
				fwrite($fpScriptfile, $mypath . "/convert_ntsc_to_image " . $source . " " . $dest . "\n");
				fwrite($fpXml, '<vob file="' . $dest . '" pause="1" />');


				foreach ($files as $index => &$file) {
					$ftype = $file->ftype;
					$source = $file->furl;//fixed_20160920
					$targetfile = $file->ftsurl;//fixed_20160920

					//if(strtoupper($ftype) == "APPLICATION"){
						if (
							strtoupper(substr($source, -3)) == "MOV" 
							|| strtoupper(substr($source, -3)) == "MP4" 
							|| strtoupper(substr($source, -3)) == "AVI"
							|| strtoupper(substr($source, -3)) == "MPG"
							|| strtoupper(substr($source, -3)) == "M4V"
                            || strtoupper(substr($source, -3)) == "F4V"
							|| strtoupper(substr($source, -3)) == "WMV"
							|| strtoupper(substr($source, -4)) == "MPEG"
                          	|| strtoupper(substr($source, -3)) == "3GP"
                          	|| strtoupper(substr($source, -3)) == "MTS"
                          	|| strtoupper(substr($source, -3)) == "3G2"
						)
						{
							$ftype = "video";
						}
					//}                  
                  
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
						//file check 1
						if(!file_exists($source)){
							//AWS S3 Source url s3://burunvideo/dvdzip/%s/%s, $item->id, $targetfile
							shell_exec(sprintf("s3cmd get s3://burunvideo/%s %s", $targetfile, $source));							
						}
						
						//file size check
						if(filesize($source) == 0){
							unlink($source);
						}
						//file check 2
						if(!file_exists($source)){
							//AWS S3 Source url s3://burunvideo/dvdzip/%s/%s, $item->id, $targetfile
							shell_exec(sprintf("s3cmd get s3://burunvideo/dvdzip/%s/%s %s", $item->id, $targetfile, $source));							
						}
						
						//file size check small than 200 kbytes
						//if((filesize($source) == 0) || (filesize($source) * 0.0009765625 < 200)){
						//	unlink($source);
						//	continue;
						//}
						if((filesize($source) == 0)){
							unlink($source);
							continue;
						}                      

						// file broken check
						// $brokencheck_cmd = "/usr/local/bin/ffprobe -v error " . $source . " 2>&1";
						$brokencheck_cmd = "$(which ffprobe) -v error " . $source . " 2>&1";
						$brokenfileCheckResult = shell_exec($brokencheck_cmd);
						if(!empty($brokenfileCheckResult)){
							//echo "error occur ->". $brokenfileCheckResult;
							//echo "canceled file -> ".$source;
							unlink($source);
							continue;
						}                      
						////////////////////////////////////////////////////////////////////////////////////////////////////////

						$dest = "dvd-" . $item->id . "-" . $file->id . ".mpeg";
                      	$fileText = trim($file->ct_caption);
                      	if(isset($fileText) && !empty($fileText)){
                        	//fwrite($fpScriptfile, $mypath . "/convert_ntsc_to_mpeg " . $source . " " . $dest . "\n");
                        	//fwrite($fpScriptfile, $mypath . "/convert_ntsc_to_mpegWithText " . $source . " " . $dest ." '". addslashes($fileText) ."'\n");
                          	fwrite($fpScriptfile, $mypath . '/convert_ntsc_to_mpegWithText ' . $source . ' ' . $dest .' "'. addslashes($fileText) .'"');
                          	fwrite($fpScriptfile, "\n");
                        } else {
                        	fwrite($fpScriptfile, $mypath . "/convert_ntsc_to_mpeg " . $source . " " . $dest . "\n");
                        }                      	
						
						fwrite($fpXml, '<vob file="' . $dest . '" />');
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
						//file check 1
						if(!file_exists($source)){
							//AWS S3 Source url s3://burunvideo/dvdzip/%s/%s, $item->id, $targetfile
							shell_exec(sprintf("s3cmd get s3://burunvideo/%s %s", $targetfile, $source));							
						}
						
						//file size check
						if(filesize($source) == 0){
							unlink($source);
						}
						//file check 2
						if(!file_exists($source)){
							//AWS S3 Source url s3://burunvideo/dvdzip/%s/%s, $item->id, $targetfile
							shell_exec(sprintf("s3cmd get s3://burunvideo/dvdzip/%s/%s %s", $item->id, $targetfile, $source));							
						}
						
						//file size check
						if(filesize($source) == 0){
							unlink($source);
							continue;
						}
						////////////////////////////////////////////////////////////////////////////////////////////////////////

						$dest = "dvd-" . $item->id . "-" . $file->id . ".mpeg";
                      	$fileText = trim($file->ct_caption);
                      	if(isset($fileText) && !empty($fileText)){
                        	//fwrite($fpScriptfile, $mypath . "/convert_ntsc_to_image " . $source . " " . $dest . "\n");
                          	//fwrite($fpScriptfile, $mypath . "/convert_ntsc_to_imageWithText " . $source . " " . $dest . " '" . addslashes($fileText) . "'\n");
                          	fwrite($fpScriptfile, $mypath . '/convert_ntsc_to_imageWithText ' . $source . ' ' . $dest . ' "' . addslashes($fileText) . '"');
                          	fwrite($fpScriptfile, "\n");
                        } else {
                        	fwrite($fpScriptfile, $mypath . "/convert_ntsc_to_image " . $source . " " . $dest . "\n");  
                        }                      
						
						fwrite($fpXml, '<vob file="' . $dest . '" />');
					}
				}

				//fwrite($fpXml, '<vob file="' . $dest . '" />');

				// end shell script file
				fwrite($fpScriptfile, "dvdauthor -o dvd -x dvd.xml\n");

				// zip file
				fwrite($fpScriptfile, "zip -r  dvd-" . $item->id . ".zip dvd\n");

				// move file
				//	fwrite($fpScriptfile, "cd ..\n");
				//	fwrite($fpScriptfile, "mv -f " .  $web_absolute_uploadpath . $item->id . " " . $s3_dir . "\n" );




				fclose($fpScriptfile);

				// end dvd xml file
				fwrite($fpXml, "</pgc></titles></titleset>");
				fwrite($fpXml, "</dvdauthor>");
				fclose($fpXml);

				// write dvd info
				$dvd_info = $web_absolute_uploadpath . $item->id . "/dvd/" . $item->id . ".info";
				$fpinfo = fopen($dvd_info, "w+");
				$torderid = sprintf("A%'.08d", $item->id);
				fwrite($fpinfo, "\norderid : " . $torderid);
				fwrite($fpinfo, "\ndvdtitle : " . $item->dvdtitle);
				fwrite($fpinfo, "\ndvdcount : " . $item->dvdcount);

				$shipping = DB::select('select n.* from order_shipping n where n.orderid=?', [ $item->id]);
				foreach ($shipping as $idx => $shipitem) {
					fwrite($fpinfo, "\n Ship No " . ($idx + 1 ));
					fwrite($fpinfo, ",   user : " . $shipitem->firstname . " " . $shipitem->lastname);
					fwrite($fpinfo, " ,  street : " . $shipitem->street);
					fwrite($fpinfo, " ,  city : " . $shipitem->city);
					fwrite($fpinfo, " ,  state : " . $shipitem->state);
					fwrite($fpinfo, " ,  zipcode : " . $shipitem->zipcode);
					fwrite($fpinfo, " ,  dvdcount : " . $shipitem->dvdcount);
				}
				fclose($fpinfo);

				// run file
				$debug_output_path2 = $web_absolute_uploadpath . $item->id . "/convert2.log";
				$convertcmd = $shellcmd . " " . $scriptfile . " >> " . $debug_output_path2 . " 2>&1";
				$result = shell_exec($convertcmd);
              
				$deleteDvdFolderCommand = "rm -rf " . $web_absolute_uploadpath . $item->id . "/dvd";
					
				shell_exec($deleteDvdFolderCommand);

				//$s3_upload_zip = "dvd-" . $item->id . ".zip";
               	//shell_exec("!#/bin/bash shopt -s extglob; set +H");
              	//shell_exec("set +H");
				//$deleteFolderCommand = 'rm -v !("convert2.log"|"' . $s3_upload_zip . '")';
				//shell_exec($deleteFolderCommand);
              
              	//shell_exec(sprintf("rm -f !('convert2.log'|'%s')", $s3_upload_zip));
              	//shell_exec("find . -type f -not \( -name '*zip' -or -name '*log' \) -delete");
              	//self::deletefiles($web_absolute_uploadpath . $item->id."/");

              
				shell_exec(sprintf("s3cmd put -r --storage-class STANDARD_IA --acl-public %s/ s3://burunvideo/dvdzip/%s/", $web_absolute_uploadpath . $item->id, $item->id));

				shell_exec(sprintf("rm -rf %s", $web_absolute_uploadpath . $item->id));
				// make zip directory
				//self::zipDir($dvd_dir, $dvd_zip);
				// move to s3bucket
				//$movecmd = "mv -f " . $dvd_dir . " " . $s3_dir;
				//shell_exec($movecmd);
				// update order save
				$currentOrder = Order::find($item->id);
				$currentOrder->zipurl = $s3_url;
				$currentOrder->status = 2;
				$currentOrder->burn_lock = 0;              
				$currentOrder->save();
			}
          
			if($lockAppId > 0){
				self::unlock_appId($lockAppId);
			}          
          
		} catch (Exception $e) {
			fwrite($output, "error\n");
			Log::info("exception:".$e->getMessage()."\n".$e->getTraceAsString());
		}
		fwrite($output, "\n" . date('Y-m-d H:i:s', time()) . " end convert\n");
		fclose($output);
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments() {
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions() {
		return array();
	}

	/**
	 * Returns the size of a file without downloading it, or -1 if the file
	 * size could not be determined.
	 *
	 * @param $url - The location of the remote file to download. Cannot
	 * be null or empty.
	 *
	 * @return The size of the file referenced by $url, or -1 if the size
	 * could not be determined.
	 */
	static function getRemoteFileSize($url) {
		// Assume failure.
		$result = -1;
		$curl = curl_init($url);

		// Issue a HEAD request and follow any redirects.
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

		$data = curl_exec($curl);
		curl_close($curl);

		if ($data) {
			$content_length = "unknown";
			$status = "unknown";

			if (preg_match("/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches)) {
				$status = (int) $matches[1];
			}

			if (preg_match("/Content-Length: (\d+)/", $data, $matches)) {
				$content_length = (int) $matches[1];
			}

			// http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
			if ($status == 200 || ($status > 300 && $status <= 308)) {
				$result = $content_length;
			}
		}

		return $result;
	}

	static $lock_file = null;
	static $lock_files = array();
  
	static function lock_app() {
		self::$lock_file = fopen('/tmp/order-convert.pid', 'c');

	    $got_lock = flock(self::$lock_file, LOCK_EX | LOCK_NB, $wouldblock);
		Log::info("lock status: got:".$got_lock. " block:".$wouldblock);
    	if (!$got_lock && !$wouldblock) {
        	throw new Exception(
            	"Unexpected error opening or locking lock file. Perhaps you " .
            	"don't  have permission to write to the lock file or its " .
            	"containing directory?"
        	);
    	}
    	else if (!$got_lock && $wouldblock) {
        	throw new Exception("Another instance is already running; terminating.\n");
    	}

	    // Lock acquired; let's write our PID to the lock file for the convenience
	    // of humans who may wish to terminate the script.
	    ftruncate(self::$lock_file, 0);
	    fwrite(self::$lock_file, getmypid() . "\n");
	}

	static function unlock_app() {
		Log::info("unlocking");
	    // All done; we blank the PID file and explicitly release the lock 
	    // (although this should be unnecessary) before terminating.
	    ftruncate(self::$lock_file, 0);
	    flock(self::$lock_file, LOCK_UN);
	}

	static function unlock_appId($appId) {
		Log::info("unlocking AppId = ". $appId);
	    // All done; we blank the PID file and explicitly release the lock 
	    // (although this should be unnecessary) before terminating.
	    ftruncate(self::$lock_files[$appId-1], 0);
	    flock(self::$lock_files[$appId-1], LOCK_UN);
	}
  
  	static function multi_lockapp($locklimit) {

		for ($lockcount = 1; $lockcount <= $locklimit;$lockcount ++){

			if($lockcount == 1){
				$lockfilepath = '/tmp/order-convert.pid';
			}else{
				$lockfilepath = '/tmp/order-convert'.$lockcount.'.pid';	
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
				echo ("Another instance is already running on this processId Number ".$lockcount."; terminating.\n");
              	Log::info("Another instance is already running on this processId Number AppId = ". $lockcount);
				continue;
			}

			// Lock acquired; let's write our PID to the lock file for the convenience
			// of humans who may wish to terminate the script.
			ftruncate(self::$lock_files[$lockcount-1], 0);
			fwrite(self::$lock_files[$lockcount-1], getmypid() . "\n");
			Log::info("locking AppId = ". $lockcount);
			return $lockcount;

		}

	}

	static function multi_unlockapp($locklimit) {

		for ($lockcount = 1; $lockcount <= $locklimit;$lockcount ++){

			//$lockfilepath = '/tmp/order-convert'.$lockcount.'.pid';
			Log::info("unlocking");
			// All done; we blank the PID file and explicitly release the lock 
			// (although this should be unnecessary) before terminating.
			ftruncate(self::$lock_files[$lockcount-1], 0);
			flock(self::$lock_files[$lockcount-1], LOCK_UN);
		}
	}
  
    static function deletefiles($path)
    {

        $extensionsToKeep = array(
            "log",
          	"zip"
        );

        $dirList = glob($path . '*');

        foreach ($dirList as $file) {

          $fileExtArr = explode('.', $file);
          $fileExt = $fileExtArr[count($fileExtArr)-1];
          if(!in_array($fileExt, $extensionsToKeep)){
            unlink($file);
          }
        }
    }
    
    static function num_cpus()
    {
      $numCpus = 1;
      if (is_file('/proc/cpuinfo'))
      {
        $cpuinfo = file_get_contents('/proc/cpuinfo');
        preg_match_all('/^processor/m', $cpuinfo, $matches);
        $numCpus = count($matches[0]);
      }
      else if ('WIN' == strtoupper(substr(PHP_OS, 0, 3)))
      {
        $process = @popen('wmic cpu get NumberOfCores', 'rb');
        if (false !== $process)
        {
          fgets($process);
          $numCpus = intval(fgets($process));
          pclose($process);
        }
      }
      else
      {
        $process = @popen('sysctl -a', 'rb');
        if (false !== $process)
        {
          $output = stream_get_contents($process);
          preg_match('/hw.ncpu: (\d+)/', $output, $matches);
          if ($matches)
          {
            $numCpus = intval($matches[1][0]);
          }
          pclose($process);
        }
      }
      
      return $numCpus;
    }    
  
}


