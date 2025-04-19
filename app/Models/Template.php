<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 
        'department_id',
        'background_front',
        'background_back',
        'description',
        'is_active',
        'editable_fields',
        'back_content',
        'logo_x',
        'logo_y',
        'logo_width',
        'text_start_x',
        'text_start_y'
    ];
    
    protected $casts = [
        'editable_fields' => 'array',
        'is_active' => 'boolean',
        'logo_x' => 'float',
        'logo_y' => 'float',
        'logo_width' => 'float',
        'text_start_x' => 'float',
        'text_start_y' => 'float'
    ];
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
