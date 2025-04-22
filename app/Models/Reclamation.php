<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    use HasFactory;

    protected $table = 'reclamations';

    // Correction: Retirer 'date_creation' car 'created_at' est géré par timestamps()
    protected $fillable = [
        'contenu',
        'statut',
        // 'date_creation', // Retiré
        'utilisateur_id',
        'reservation_id',
    ];

    // Correction: Retirer le cast pour 'date_creation'
    // protected $casts = [
    //     'date_creation' => 'date',
    // ];

    // Si vous n'utilisez PAS $table->timestamps() dans la migration, alors décommentez:
    // public $timestamps = false;
    // Et remettez 'date_creation' dans $fillable et $casts.
    // Mais il est recommandé d'utiliser timestamps().

    // --- Relations ---

    // Correction: Relation vers le modèle User
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id'); // ÉTAIT: Utilisateur
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}