<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendLowStockEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public int $productId;
    public int $quantity;

    public function __construct(int $productId, int $quantity)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
    // Placeholder: implement email sending via Mailable or external service
    }
}
