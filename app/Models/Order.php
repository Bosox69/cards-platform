<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['user_id', 'client_id', 'order_status_id', 'comment'];
    
    /**
     * Relation avec l'utilisateur qui a passé la commande
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Relation avec le client (entreprise)
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    /**
     * Relation avec le statut de la commande
     */
    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }
    
    /**
     * Relation avec les éléments de la commande
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    /**
     * Relation avec l'historique des statuts
     */
    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
    
    /**
     * Calcule le nombre total de cartes dans la commande
     */
    public function getTotalCardsAttribute()
    {
        return $this->orderItems->sum('quantity');
    }
    
    /**
     * Détermine si la commande est en attente
     */
    public function isPending()
    {
        return in_array($this->orderStatus->name, ['Nouvelle', 'En traitement']);
    }
    
    /**
     * Détermine si la commande est en cours de production
     */
    public function isInProduction()
    {
        return $this->orderStatus->name === 'En production';
    }
    
    /**
     * Détermine si la commande est complétée
     */
    public function isCompleted()
    {
        return in_array($this->orderStatus->name, ['Expédié', 'Complété']);
    }
    
    /**
     * Détermine si la commande est annulée
     */
    public function isCancelled()
    {
        return $this->orderStatus->name === 'Annulé';
    }
}
