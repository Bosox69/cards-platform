<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'file_path', 'description', 'is_double_sided', 'editable_fields', 'is_active'];
    
    protected $casts = [
        'editable_fields' => 'array',
        'is_double_sided' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
