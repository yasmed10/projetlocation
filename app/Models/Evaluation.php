<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $table = 'evaluations';

    protected $fillable = [
        'note',
        'commentaire',
        'date',
        'objet_id',
        'evaluateur_id',
        'evalue_id',
    ];

    protected $casts = [
        'note' => 'integer',
        'date' => 'date',
    ];

    // --- Relations ---

    public function objet()
    {
        return $this->belongsTo(Objet::class);
    }

    // Correction: Relation vers le modèle User
    public function evaluateur()
    {
        return $this->belongsTo(User::class, 'evaluateur_id'); // ÉTAIT: Utilisateur
    }

    // Correction: Relation vers le modèle User
    public function evalue()
    {
        return $this->belongsTo(User::class, 'evalue_id'); // ÉTAIT: Utilisateur
    }
}