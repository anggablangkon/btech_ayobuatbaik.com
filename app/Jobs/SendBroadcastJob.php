<?php

namespace App\Jobs;

use App\Helpers\Fonnte;
use App\Models\Broadcast;
use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $broadcast;

    /**
     * Create a new job instance.
     */
    public function __construct(Broadcast $broadcast)
    {
        $this->broadcast = $broadcast;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->broadcast->update(['status' => 'processing']);

            $numbers = $this->getTargetNumbers();
            
            if (empty($numbers)) {
                $this->broadcast->update(['status' => 'completed', 'total_count' => 0]);
                return;
            }

            $total = count($numbers);
            $this->broadcast->update(['total_count' => $total]);

            // Fonnte recommends max 50-100 numbers per request, let's use 20 to be safe and responsive
            $chunks = array_chunk($numbers, 20);
            $processed = 0;

            $imageUrl = $this->broadcast->image_path ? asset($this->broadcast->image_path) : null;
            
            // Force HTTPS for image URL to ensure Fonnte can access it
            if ($imageUrl && str_starts_with($imageUrl, 'http://')) {
                $imageUrl = str_replace('http://', 'https://', $imageUrl);
            }

            if ($imageUrl) {
                Log::info("Broadcast Image URL: " . $imageUrl);
            }

            foreach ($chunks as $chunk) {
                // Normalize numbers: 08xxx -> 628xxx
                $normalizedChunk = array_map(function($number) {
                    $number = trim($number);
                    if (str_starts_with($number, '0')) {
                        return '62' . substr($number, 1);
                    }
                    if (str_starts_with($number, '+62')) {
                        return substr($number, 1);
                    }
                    return $number;
                }, $chunk);
                
                $targetString = implode(',', $normalizedChunk);
                
                try {
                    $response = Fonnte::send($targetString, $this->broadcast->message, $imageUrl);
                    $json = json_decode($response, true);

                    if (isset($json['status']) && !$json['status']) {
                        Log::error("Broadcast Job Fonnte Error: " . ($json['reason'] ?? 'Unknown error'), ['response' => $json]);
                        // Optional: Mark as partially failed? For now just log.
                        // But if it's a token issue, maybe should stop?
                        // Let's just log for now to debug.
                    }
                } catch (\Exception $e) {
                    Log::error("Broadcast Job Partial Fail: " . $e->getMessage());
                }

                $processed += count($chunk);
                $this->broadcast->update(['processed_count' => $processed]);
                
                sleep(2); // Small delay between chunks
            }

            $this->broadcast->update(['status' => 'completed']);

        } catch (\Exception $e) {
            Log::error("Broadcast Job Failed: " . $e->getMessage());
            $this->broadcast->update(['status' => 'failed']);
        }
    }

    private function getTargetNumbers(): array
    {
        $numbers = [];
        $target = $this->broadcast->target;

        if ($target === 'donors') {
            $numbers = Donation::whereNotNull('donor_phone')
                        ->where('donor_phone', '!=', '')
                        ->pluck('donor_phone')
                        ->unique()
                        ->values()
                        ->toArray();
        } elseif ($target === 'test') {
            // For testing, just sending to the one in target_data
            if (!empty($this->broadcast->target_data['test_number'])) {
                $numbers[] = $this->broadcast->target_data['test_number'];
            }
        } elseif ($target === 'csv_audience') {
            // Ambil dari hasil parsing CSV yg disimpan di target_data
            if (!empty($this->broadcast->target_data['csv_numbers'])) {
                $numbers = $this->broadcast->target_data['csv_numbers'];
            }
        }
        // Add other targets here (e.g. 'all', 'subscribers') if you have Users table with phones

        return $numbers;
    }
}
