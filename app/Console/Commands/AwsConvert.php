<?php

namespace burnvideo\Console\Commands;

use burnvideo\Models\Order;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Log;
use DB;

class AwsConvert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:awsConvert {orderId} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One order convert from AWS Lambda function.';

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
        $orderId = $this->argument('orderId');
        // Log::useFiles(storage_path() . '/logs/command.log');
        // Log::info("create order command : \n".$orderId);

        $awsConvertOrder = Order::find($orderId);

        if (!empty($awsConvertOrder)) {

            $orderFiles = DB::select('select DISTINCT f.furl as uniquefurl, f.* from file f '
                . ' inner join order_file of on of.fid = f.id '
                . ' inner join orders o on o.id = of.oid '
                . ' where o.id = ' . $orderId . ' order by f.file_index, f.id asc;');

            $fileJson = [];
            foreach ($orderFiles as $index => &$file) {
                $file_name = $file->ftsurl;
                $file_caption = trim($file->ct_caption);
                $oneFileJson = [
                    "name" => $file_name,
                    "caption" => $file_caption,
                ];
                array_push($fileJson, $oneFileJson);
            }
            $orderPayLoad = [
                "orderid" => $orderId,
                "files" => $fileJson,
            ];
            $orderJsonStr = json_encode($orderPayLoad);

            //$orderJsonStr = str_replace("\\\\'", "\\\\\\'", $orderJsonStr);
            //$orderJsonStr = str_replace('\\\\"', '\\\\\\"', $orderJsonStr);
            //$orderJsonStr = str_replace('"\"', '"\\\\\\"', $orderJsonStr);
            //$orderJsonStr = str_replace("'\'", "'\\\\\\'", $orderJsonStr);
            //$orderJsonStr = str_replace("\\\\\\\\", "\\\\\\", $orderJsonStr);
            // aws command example
            // aws lambda invoke --function-name BatchSubmitter --payload '{"orderid":"101","files":[{"name":"test.mp4","caption":"Test-batch"}]}' --region=us-east-1 /tmp/logs
            // $awsCommand = "aws lambda invoke --function-name BatchSubmitter --payload '" . $orderJsonStr ."' --region=us-east-1 /tmp/logs";
            // Log::info("created order files : \n".sprintf("aws lambda invoke --function-name BatchSubmitter --payload '%s' --region=us-east-1 /tmp/logs", $orderJsonStr));
            echo ("created order files : \n".sprintf("aws lambda invoke --function-name BatchSubmitter --payload '%s' --region=us-east-1 /tmp/logs", $orderJsonStr));
            $result = shell_exec(sprintf("aws lambda invoke --function-name BatchSubmitter --payload '%s' --region=us-east-1 /tmp/logs", $orderJsonStr));
            // Log::info("aws order result : \n".);

            $awsConvertOrder->burn_lock = 1;
            $awsConvertOrder->burn_app = 1;//AWS Lambda Convert
            $awsConvertOrder->save();

        }


    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return array(
            array('orderId', InputArgument::REQUIRED, 'order Id required.'),
        );

    }
}
