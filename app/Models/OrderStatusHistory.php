<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;
    
    protected $fillable = ['order_id', 'order_status_id', 'user_id', 'comment'];
    
    /**
     * Relation avec la commande
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Relation avec le statut
     */
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }
    
    /**
     * Relation avec l'utilisateur qui a effectué le changement
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
