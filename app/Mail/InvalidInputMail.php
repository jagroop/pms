<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\Tracker;

class InvalidInputMail extends Mailable
{
    use Queueable, SerializesModels;

    public $css = [
      'table'       => 'padding-top: 5px; padding-bottom: 5px; table-layout: fixed;width: 100%;border-collapse: collapse;',
      'th'          => 'border: 1px solid blue; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;',
      'td'          => 'border: 1px solid #999; word-wrap: break-word; padding: 5px;',
      'red_color'   => 'background: #ffe3e3',
      'green_color' => 'background: #d3f9d8',
    ];
    
    public $tasks;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Warning: Invalid tasks inputs.')
            ->view('email.invalid_inputs');
    }
}