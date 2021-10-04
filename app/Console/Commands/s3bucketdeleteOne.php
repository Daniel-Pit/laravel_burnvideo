<?php

namespace burnvideo\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use DB;
use Log;

use burnvideo\Models\S3HistoryModel;
use Exception;

class s3bucketdeleteOne extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 's3bucket:delete-one';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'burnvideo s3 bucket One Order delete.';

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
		$getOptionOrderNumber = $this->argument('oNum');
		
		try {

			// get movie
			$files = DB::select('select f.* from file f '
							. ' inner join order_file of on of.fid = f.id '
							. ' inner join orders o on o.id = of.oid '
							. ' where o.id = ' . $getOptionOrderNumber);


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
			echo sprintf("s3cmd del -r -f s3://burunvideo/dvdzip/%s", $getOptionOrderNumber)."\n";;
			shell_exec(sprintf("s3cmd del -r -f s3://burunvideo/dvdzip/%s", $getOptionOrderNumber));

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
			array('oNum'),
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

}
