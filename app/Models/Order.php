<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->foreignId('client_id')->constrained();
    $table->foreignId('order_status_id')->constrained('order_status');
    $table->text('comment')->nullable();
    $table->timestamps();
    $table->softDeletes(); // Pour garder un historique des commandes supprimées
});


class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given order can be viewed by the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return bool
     */
    public function view(User $user, Order $order)
    {
        // Les administrateurs peuvent voir toutes les commandes
        if ($user->isAdmin()) {
            return true;
        }
        
        // Les clients ne peuvent voir que leurs propres commandes
        return $order->client_id === $user->client_id;
    }
    
    /**
     * Determine if the user can update the order.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return bool
     */
    public function update(User $user, Order $order)
    {
        // Seuls les administrateurs peuvent mettre à jour une commande
        return $user->isAdmin();
    }
    
    /**
     * Determine if the user can delete the order.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return bool
     */
    public function delete(User $user, Order $order)
    {
        // Seuls les administrateurs peuvent supprimer une commande
        return $user->isAdmin();
    }
    
    /**
     * Determine if the user can create orders.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        // Les administrateurs et les clients peuvent créer des commandes
        return true;
    }


class Order extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'client_id', 'order_number', 'status_id', 'notes', 'completed_at'];
    
    protected $casts = [
        'completed_at' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }
    
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
