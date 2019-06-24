<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\StatusReminderEmail;
use Illuminate\Support\Facades\Mail;

class StatusReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:status_remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remind Team members to put status';

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
        $users = User::where('email', '!=', 'jagroop.singh@kindlebit.com')->get()->map(function($user){
          return $user->email;
        })->toArray();
        Mail::to('jagroop.singh@kindlebit.com')->cc($users)->send(new StatusReminderEmail);
    }
}
