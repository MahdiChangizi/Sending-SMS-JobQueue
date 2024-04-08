<?php

namespace App\Jobs;

use App\Models\NotifyCode;
use App\Models\User;
use App\Notifications\SendVerificationCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendingSMS implements ShouldQueue, ShouldBeEncrypted  # To encrypt errors
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    public $tries = 5; # Number of attempts to execute
    public $retryAfter = 10; # Delay time between attempts
    public $timeout = 120; # The maximum execution time of the job


    public int $code;
    public function __construct(public User $user)
    {
        $this->onQueue("sendingSMS");
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        try {
            $this->code = NotifyCode::generateCode($this->user); # create code
            $this->user->notify(new SendVerificationCode(code: $this->code, user: $this->user)); #send sms
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error occurred during code generation and SMS sending: ' . $e->getMessage());
        }
    }
}
