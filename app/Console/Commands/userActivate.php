<?php

namespace burnvideo\Console\Commands;

use Illuminate\Console\Command;

use DB;
use Sentinel;
use Activation;

class userActivate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:userActivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'user upgrading to sentinel user';

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
        $instanceNumber = config('services.instanceNumber');
        echo "current Instance Number ".$instanceNumber;
        
		$users = DB::select('select u.* from users u;');
		
		foreach( $users as $key => &$user){
			$activeUser = Sentinel::findById($user->id);

			$activation = Activation::create($activeUser);

		}
    }
}
