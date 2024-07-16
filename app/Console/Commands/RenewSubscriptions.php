<?php

namespace App\Console\Commands;

use App\Jobs\RenewSubscription;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class RenewSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:renew-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renew subscriptions which renewal_at date is today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        $subjectToRenewing = Subscription::with(['user','plan'])->whereDate('renewal_at',$today)->get();
        $subjectToRenewing->each(function($subscription){
            RenewSubscription::dispatch($subscription);
        });
        $this->info($subjectToRenewing->count() . ' subscriptions queued to be renewed.');
        $this->warn('Don\'t forget to get the queue working... (php artisan queue:work)');
    }
}
