<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->cascadeOnDelete();
    $table->foreignId('template_id')->constrained();
    $table->foreignId('department_id')->constrained();
    $table->integer('quantity');
    $table->boolean('is_double_sided')->default(false);
    $table->string('pdf_preview')->nullable();
    $table->timestamps();
});

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
