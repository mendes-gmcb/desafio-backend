<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\User;
use App\Notifications\PaymentReceived;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected User $payee, 
        protected Transaction $transaction
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->payee->notify(
                (new PaymentReceived($this->transaction))
                    ->delay(now()->addMinutes(1))
            );

            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/email.log'),
              ])->info('email enviado com sucesso');
        } catch (\Throwable $th) {
            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/email.log'),
              ])->error($th->getMessage());    
        }
    }
}
