<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    
    protected $fillable = ['client_id', 'name', 'code', 'description', 'is_active'];
    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    public function templates()
    {
        return $this->belongsToMany(Template::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
