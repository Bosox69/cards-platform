<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * La commande.
     *
     * @var \App\Models\Order
     */
    public $order;

    /**
     * L'ancien statut.
     *
     * @var string
     */
    public $oldStatus;

    /**
     * Le nouveau statut.
     *
     * @var string
     */
    public $newStatus;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Order  $order
     * @param  string  $oldStatus
     * @param  string  $newStatus
     * @return void
     */
    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Mise à jour de votre commande #' . $this->order->id)
                    ->markdown('emails.orders.status_update');
    }
}
