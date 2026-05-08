<?php

namespace App\Jobs;

use App\Helpers\Fonnte;
use App\Helpers\FonnteMessageHelper;
use App\Models\QurbanParticipant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendQurbanJob implements ShouldQueue
{
    use Queueable;

    protected $qurbanParticipant;
    /**
     * Create a new job instance.
     */
    public function __construct(QurbanParticipant $qurbanParticipant)
    {
        $this->qurbanParticipant = $qurbanParticipant;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $message = FonnteMessageHelper::claimKuponQurbanMessage($this->qurbanParticipant);
        Fonnte::send($this->qurbanParticipant->contact_number, $message);
        $this->qurbanParticipant->update(["status" => "sended"]);
    }
}
