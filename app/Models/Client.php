<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'address', 'city', 'postal_code', 'country', 'phone', 'email', 'contact_person', 'is_active'];
    
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function departments()
    {
        return $this->hasMany(Department::class);
    }
    
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
