<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\EmailNotification;
use Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct($template, $subject, $data)
    {
        $this->template     =   $template;
        $this->data         =   $data; 
        $this->subject      =   $subject; 
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = new EmailNotification($this->template, $this->subject, $this->data);
        Mail::to($this->data['send_to'])->send($email);
    }
}
