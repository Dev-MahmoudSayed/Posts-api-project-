<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FetchRandomUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response = Http::get('https://randomuser.me/api/');

            if ($response->successful()) {
                Log::info('Random User Results:', $response->json()['results']);
            } else {
                Log::error('Failed to fetch random user data');
            }
        } catch (\Exception $e) {
            Log::error('Error fetching random user: ' . $e->getMessage());
        }

    }
}
