<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model // Nom de classe OK
{
    use HasFactory;

    protected $table = 'categories'; // OK

    protected $fillable = [
        'nom', // OK
    ];

    // Relation OK
    public function objets()
    {
        return $this->hasMany(Objet::class);
    }

    // Ajout: Accessor pour le slug (si utilisé dans la vue admin/annonces)
    public function getSlugAttribute() {
        return \Illuminate\Support\Str::slug($this->nom);
    }
}