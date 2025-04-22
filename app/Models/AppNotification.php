<?php

// Correction: Changer le namespace si vous déplacez/renommez
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Correction: Renommer la classe pour éviter conflit
class AppNotification extends Model // ÉTAIT: Notification
{
    use HasFactory;

    // Correction: Adapter le nom de table si nécessaire (si vous changez aussi la migration)
    // Si la table reste 'notifications', c'est OK.
    protected $table = 'notifications';

    protected $fillable = [
        'contenu',
        'contenu_email',
        'envoyee',
        'lue',
        'utilisateur_id',
        'annonce_id',
        'reservation_id',
    ];

    protected $casts = [
        'envoyee' => 'boolean',
        'lue' => 'boolean',
    ];

    // --- Relations ---

    // Correction: Relation vers le modèle User
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id'); // ÉTAIT: Utilisateur
    }

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}