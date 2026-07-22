<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RetrainForecastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     * SARIMA retraining for 8 models can take several minutes.
     */
    public $timeout = 600;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 1;

    /**
     * Execute the job.
     *
     * Calls the FastAPI /retrain endpoint which merges
     * CSV baseline + MySQL delta and refits all 8 SARIMA models.
     */
    public function handle(): void
    {
        $apiUrl = rtrim(config('services.sarima.url', 'http://localhost:8000'), '/');

        try {
            Log::info('[RetrainForecastJob] Starting SARIMA retrain via FastAPI...');

            $response = Http::withoutVerifying()
                ->timeout(600)
                ->post($apiUrl . '/retrain');

            if ($response->successful()) {
                $data = $response->json();
                Log::info('[RetrainForecastJob] Retrain completed.', [
                    'attractions_retrained' => $data['attractions_retrained'] ?? 0,
                    'last_synced' => $data['last_synced'] ?? null,
                ]);
            } else {
                Log::error('[RetrainForecastJob] FastAPI returned error.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('[RetrainForecastJob] Failed to reach FastAPI.', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
