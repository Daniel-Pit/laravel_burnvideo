<?php

namespace burnvideo\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Log;
use Exception;

class ordermoniter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:watcher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User Order Watcher';

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
        $web_absolute_path = config('services.appRootPath');

        $web_absolute_uploadpath = $web_absolute_path . "public/uploads/order/";

        //
        try {
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
                $this->call('order:awsConvert', [
                    'orderId' => (string)$item->id, '--queue' => 'default'
                ]);
            }
        } catch (Exception $e) {
            Log::info("unknown exception:".$e->getMessage()."\n".$e->getTraceAsString());
        }
    }
}
