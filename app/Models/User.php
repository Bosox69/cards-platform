<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'client_id',
        'is_admin',
        'phone',
        'job_title',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'last_login_at' => 'datetime',
    ];
    
    /**
     * Relation avec le client (entreprise).
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    /**
     * Relation avec les commandes.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Vérifie si l'utilisateur est administrateur.
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }
    
    /**
     * Vérifie si l'utilisateur est un client.
     */
    public function isClient()
    {
        return !$this->is_admin && $this->client_id !== null;
    }
    
    /**
     * Enregistre la date de dernière connexion.
     */
    public function logLogin()
    {
        $this->last_login_at = now();
        $this->save();
    }
}
