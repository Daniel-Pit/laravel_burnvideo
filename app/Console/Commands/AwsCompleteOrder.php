<?php

namespace burnvideo\Console\Commands;

use burnvideo\Models\Order;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Log;
use DB;

class AwsCompleteOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:awsCompleteOrder {orderId} {status} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One order complete from AWS Lambda function.';

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
        $orderId = $this->argument('orderId');
        $status = $this->argument('status');
        // Log::useFiles(storage_path() . '/logs/command.log');
        // Log::info("completed order ID : \n".$orderId);
        // Log::info("completed order status : \n".$status);

        $awsConvertOrder = Order::find($orderId);

        if (!empty($awsConvertOrder)) {

            if ( $status == 1 ) {
                $s3_url = "https://s3.amazonaws.com/burunvideo/dvdzip/" . $orderId . "/dvd-" . $orderId . ".zip";
                $web_absolute_path = config('services.appRootPath');

                $web_absolute_uploadpath = $web_absolute_path . "public/uploads/order/";

                // write dvd info
                $dvd_info = $web_absolute_uploadpath . $orderId . "/" . $orderId . ".info";

                $dvd_dir = $web_absolute_uploadpath . $orderId;
                if (!file_exists($dvd_dir))
                    mkdir($dvd_dir);

                $fpinfo = fopen($dvd_info, "w+");
                $torderid = sprintf("A%'.08d", $orderId);
                fwrite($fpinfo, "\norderid : " . $torderid);
                fwrite($fpinfo, "\ndvdtitle : " . $awsConvertOrder->dvdtitle);
                fwrite($fpinfo, "\ndvdcount : " . $awsConvertOrder->dvdcount);

                $shipping = DB::select('select n.* from order_shipping n where n.orderid=?', [ $orderId ]);
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

                shell_exec(sprintf("s3cmd put -r --storage-class STANDARD_IA --acl-public %s/ s3://burunvideo/dvdzip/%s/", $web_absolute_uploadpath . $orderId, $orderId));
                sleep(5);

                shell_exec(sprintf("rm -rf %s", $web_absolute_uploadpath . $orderId));
                sleep(5);

                $awsConvertOrder->zipurl = $s3_url;
                $awsConvertOrder->status = 2;
                $awsConvertOrder->burn_lock = 0;

            } else {
                $awsConvertOrder->burn_lock = 2;
            }

            $awsConvertOrder->save();

        }

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return [
            ['orderId', InputArgument::REQUIRED, 'order Id required.'],
            ['status', InputArgument::REQUIRED, 'status required.'],

        ];

    }

}
