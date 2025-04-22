<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    use HasFactory;

    protected $table = 'annonces';

    protected $fillable = [
        'date_publication',
        'date_debut',
        'date_fin',
        'statut',
        'premium',
        'adresse',
        'objet_id',
        'proprietaire_id',
    ];

    protected $casts = [
        'premium' => 'boolean',
        'date_publication' => 'date',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    // --- Relations ---

    public function objet()
    {
        return $this->belongsTo(Objet::class);
    }

    // Correction: Relation vers le modèle User (qui gère la table utilisateurs)
    public function proprietaire()
    {
        return $this->belongsTo(User::class, 'proprietaire_id'); // ÉTAIT: Utilisateur
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Correction: Relation vers le modèle de notification renommé (si renommé)
    public function notifications()
    {
        // Utiliser AppNotification si vous renommez le modèle Notification
        return $this->hasMany(AppNotification::class); // OU AppNotification::class
    }

    // Ajout: Accessor pour une miniature (si nécessaire pour la vue admin/annonces)
    // Suppose que l'objet lié a des images. Prend la première image.
    public function getThumbnailAttribute()
    {
        // Vérifie si l'objet et la relation images existent et ne sont pas vides
        if ($this->objet && $this->objet->images()->exists()) {
            // Prend l'URL de la première image ou null si aucune image
            return $this->objet->images->first()->url ?? null;
        }
        return null; // Pas d'objet ou pas d'images
    }

    // Ajout: Accessor pour le titre (si le titre est sur l'objet)
    public function getTitleAttribute() {
        return $this->objet->nom ?? 'Titre non disponible';
    }

    // Ajout: Accessor pour le prix (si le prix est sur l'objet)
     public function getPriceAttribute() {
        return $this->objet->prix_journalier ?? 0;
    }

     // Ajout: Relation pour la catégorie via l'objet
     public function category() {
         // Utilise la relation 'objet' puis la relation 'categorie' de l'objet
         // Requires the 'objet' relationship to be defined correctly
         // with `belongsTo(Objet::class)` and Objet model having `belongsTo(Categorie::class)`
         // Use `with('objet.categorie')` for eager loading to avoid N+1 issues.
         // Note: This doesn't work directly as a simple relationship for filtering/sorting easily.
         // It's mainly for accessing the category name from an Annonce instance: $annonce->category->nom
         // For querying/filtering, joins are usually better (as done in AdminStatsController).
         // A more direct way if needed often is to query through the Objet model.
         // However, accessing it directly can be convenient:
         return $this->objet->categorie(); // Returns the BelongsTo relationship instance
     }

     // Relation vers l'utilisateur (alternative à 'proprietaire' pour correspondre à la vue)
      public function user()
     {
         return $this->belongsTo(User::class, 'proprietaire_id');
     }

}