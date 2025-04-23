<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    use HasFactory;

    protected $table = 'annonces';

    // !! $fillable ne contient PAS rejection_reason !!
    protected $fillable = [
        'date_publication',
        'date_debut',
        'date_fin',
        'statut', // Assurez-vous que 'archived' est une valeur possible
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

    // --- Relations (inchangÃ©es) ---
    public function objet() { return $this->belongsTo(Objet::class); }
    public function proprietaire() { return $this->belongsTo(User::class, 'proprietaire_id'); }
    public function user() { return $this->belongsTo(User::class, 'proprietaire_id'); } // Alias
    public function reservations() { return $this->hasMany(Reservation::class); }
    public function notifications() { return $this->hasMany(AppNotification::class); }

    // --- Accessors (inchangÃ©s, sauf thumbnail adaptÃ© si besoin) ---
    public function getThumbnailAttribute() {
        // Assurez-vous que la relation et l'accessor public_url existent dans Image.php
        return $this->objet?->images?->first()?->public_url ?? asset('images/placeholder.jpg');
    }
    public function getTitleAttribute() { return $this->objet->nom ?? 'Titre non disponible'; }
    public function getPriceAttribute() { return $this->objet->prix_journalier ?? 0; }
    public function getCategoryAttribute() { return $this->objet?->categorie; } // AccÃ©der Ã  l'objet Categorie

    // Helper pour le statut lisible (inchangÃ©)
    public function getStatutLabelAttribute(): string {
        $labels = [ 'pending' => 'En attente', 'active' => 'En ligne', 'rejected' => 'RejetÃ©e', 'draft' => 'Brouillon', 'archived' => 'ArchivÃ©e'];
        return $labels[$this->statut] ?? ucfirst($this->statut ?? 'Inconnu');
    }

     // Helper pour la classe CSS du statut (inchangÃ©)
    public function getStatutClassAttribute(): string {
        return \Illuminate\Support\Str::slug($this->statut ?? 'unknown');
    }
}