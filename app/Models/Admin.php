<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Potentiellement pour un Guard admin séparé
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable // Ou juste extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'mot_pass', // Attention au nom différent
    ];

    protected $hidden = [
        'mot_pass',
        // 'remember_token',
    ];

    protected $casts = [
        'mot_pass' => 'hashed',
    ];

    // Aucune relation définie DANS CETTE TABLE selon la migration fournie

    /**
     * Get the password for the user. (Si utilisé pour Auth)
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->mot_pass;
    }

     /**
     * Get the name of the password attribute. (Si utilisé pour Auth)
     *
     * @return string
     */
    public function getAuthPasswordName()
    {
        return 'mot_pass';
    }
}