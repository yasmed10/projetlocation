<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'utilisateurs';

    // !! $fillable ne contient PAS approved_at, blocked_at, cin_verified_at, cin_rejection_reason !!
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'mot_de_passe',
        'role',
        'image_profile',
        'cin_recto',
        'cin_verso',
        'archived_at'
    ];

    protected $hidden = [
        'mot_de_passe',
    ];

    // !! $casts ne contient PAS les dates correspondantes !!
    protected $casts = [
        'mot_de_passe' => 'hashed',
        'archived_at' => 'datetime'
    ];

    // --- Relations (inchangÃ©es) ---
    public function objets() { return $this->hasMany(Objet::class, 'proprietaire_id'); }
    // ... autres relations ...
    public function reclamations() { return $this->hasMany(Reclamation::class, 'utilisateur_id'); }

    // --- MÃ©thodes pour Auth (inchangÃ©es) ---
    public function getAuthPassword() { return $this->mot_de_passe; }
    public function getAuthPasswordName() { return 'mot_de_passe'; }

    // --- Accessors (inchangÃ©s) ---
    public function getFullNameAttribute(): string { return trim($this->nom . ' ' . $this->prenom); }
    public function getAvatarUrlAttribute(): string {
        if ($this->image_profile && Storage::disk('public')->exists($this->image_profile)) {
            return Storage::disk('public')->url($this->image_profile);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name ?: $this->email) . '&background=random&color=fff&size=40';
    }
     public function getCinRectoUrlAttribute(): ?string {
        return $this->cin_recto && Storage::disk('public')->exists($this->cin_recto) ? Storage::disk('public')->url($this->cin_recto) : null;
    }
     public function getCinVersoUrlAttribute(): ?string {
        return $this->cin_verso && Storage::disk('public')->exists($this->cin_verso) ? Storage::disk('public')->url($this->cin_verso) : null;
    }


    // --- MÃ©thodes Helper pour Statut (AdaptÃ©es ou RetirÃ©es) ---

    // isApproved n'a plus de sens direct sans colonne dÃ©diÃ©e.
    // Vous pouvez dÃ©finir une logique basÃ©e sur le rÃ´le si nÃ©cessaire.
    // public function isApproved(): bool { return $this->role !== 'pending_approval'; // Exemple si vous ajoutez ce rÃ´le }

    // RetirÃ© car pas de colonne blocked_at
    // public function isBlocked(): bool { return !is_null($this->blocked_at); }

    // On considÃ¨re vÃ©rifiÃ© si rÃ´le = proprietaire
    public function isCinConsideredVerified(): bool {
        return $this->role === 'proprietaire';
    }

    // NÃ©cessite vÃ©rification si PAS proprietaire ET fichiers prÃ©sents
    public function needsCinVerification(): bool {
        return $this->role !== 'proprietaire' && !empty($this->cin_recto) && !empty($this->cin_verso);
    }
}