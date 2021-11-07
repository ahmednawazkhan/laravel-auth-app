<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PINConfirmationNotification extends Notification
{
    use Queueable;
    protected $pin;
    protected $pin_confirm_url;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($pin, $pin_confirm_url)
    {
        $this->pin = $pin;
        $this->pin_confirm_url = $pin_confirm_url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Please Confirm your pin.')
            ->line($this->pin)
            ->line('Send POST request to ' . $this->pin_confirm_url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
