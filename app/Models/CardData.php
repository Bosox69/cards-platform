<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

Schema::create('card_data', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
    $table->json('data'); // Stockage des données personnalisées (nom, titre, email, etc.)
    $table->timestamps();
});

class CardData extends Model
{
    use HasFactory;
    
    protected $fillable = ['order_item_id', 'field_name', 'field_value'];
    
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
