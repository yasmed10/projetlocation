<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objet extends Model
{
    use HasFactory;

    protected $table = 'objets';

    protected $fillable = [
        'nom',
        'description',
        'ville',
        'prix_journalier',
        'etat',
        'categorie_id',
        'proprietaire_id',
    ];

    protected $casts = [
        'prix_journalier' => 'decimal:2',
    ];

    // --- Relations ---

    // Correction: Relation vers le modèle Categorie
    public function categorie()
    {
        return $this->belongsTo(Categorie::class); // ÉTAIT: Categorie sans namespace complet, mieux avec ::class
    }

    // Correction: Relation vers le modèle User
    public function proprietaire()
    {
        return $this->belongsTo(User::class, 'proprietaire_id'); // ÉTAIT: Utilisateur
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function annonces()
    {
        return $this->hasMany(Annonce::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}