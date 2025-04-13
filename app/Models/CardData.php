<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardData extends Model
{
    use HasFactory;
    
    protected $fillable = ['order_item_id', 'field_name', 'field_value'];
    
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
