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

use Parallel\Parallel;
use Parallel\Storage\ApcuStorage;

class ParallelTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parallel:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
		$web_absolute_path = config('services.appRootPath');

		$web_absolute_uploadpath = $web_absolute_path . "public/uploads/order/";
        $itemId = 9395;
        
		$deleteDvdFolderCommand = "rm -rf " . $web_absolute_uploadpath . $itemId . "/dvd";
			
		shell_exec($deleteDvdFolderCommand);
        
        self::deletefiles($web_absolute_uploadpath . $itemId."/");
    }
    
    static function deletefiles($path)
    {

        $extensionsToKeep = [ "log", "zip" ];

        if ($opendir = opendir($path)){
        //read directory
            while(($file = readdir($opendir))!= FALSE ){
                $inDirfileInfo = pathinfo($file);
                echo "file info \n";
                print_r($inDirfileInfo);
                $fileExt = $inDirfileInfo['extension'];
                echo "\nfile Extension ".$fileExt. "\n";
                if( $file !="." && $file != ".." && !in_array($fileExt, $extensionsToKeep) ){
                    echo "delete file \n";
                    print_r($path.$file);
                    echo "\n";
                    unlink($path.$file);
                }
            }
       } 
    }    
}
