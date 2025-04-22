<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    // Ajout possible: 'total_amount' si vous l'ajoutez à la migration pour les stats
    protected $fillable = [
        'client_id',
        'annonce_id',
        'date_debut',
        'date_fin',
        'statut',
        // 'total_amount', // Décommenter si ajouté
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        // 'total_amount' => 'decimal:2', // Décommenter si ajouté
    ];

    // --- Relations ---

    // Correction: Relation vers le modèle User
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id'); // ÉTAIT: Utilisateur
    }

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function reclamations()
    {
        return $this->hasMany(Reclamation::class);
    }

    // Correction: Relation vers le modèle de notification renommé
    public function notifications()
    {
        // Utiliser AppNotification si vous renommez le modèle Notification
        return $this->hasMany(AppNotification::class); // OU AppNotification::class
    }
}