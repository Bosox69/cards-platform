<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    
    protected $fillable = ['order_id', 'template_id', 'department_id', 'quantity', 'is_double_sided', 'pdf_preview'];
    
    protected $casts = [
        'is_double_sided' => 'boolean',
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function template()
    {
        return $this->belongsTo(Template::class);
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function cardData()
    {
        return $this->hasMany(CardData::class);
    }
}
