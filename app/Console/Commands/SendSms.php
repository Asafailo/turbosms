<?php

namespace App\Console\Commands;

use App\Http\Services\TurboSmsService;
use Illuminate\Console\Command;

class SendSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $turboSmsService = new TurboSmsService(['380959145603']);
        $message = $turboSmsService->sendCode();
        dd($message);
    }
}
