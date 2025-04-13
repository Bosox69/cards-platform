<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
