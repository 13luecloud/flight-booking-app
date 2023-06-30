<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $name, $bookingId, $payable; 

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $bookingId, $payable)
    {
        $this->name = $name;
        $this->bookingId = $bookingId;
        $this->payable = $payable;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('bookingconfirmed');
    }
}
