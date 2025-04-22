<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage; // Pour l'avatar_url

class User extends Authenticatable
{
    use HasFactory, Notifiable; // HasApiTokens

    protected $table = 'utilisateurs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'mot_de_passe',
        'role',
        'image_profile',
        'cin_recto',
        'cin_verso',
        // Ajouter les champs pour la logique admin si vous les créez
        // 'approved_at',
        // 'blocked_at',
        // 'cin_verified_at',
        // etc.
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'mot_de_passe',
        // 'remember_token', // Pas de colonne par défaut
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime', // Pas de colonne par défaut
        'mot_de_passe' => 'hashed',
         // Ajouter les casts pour les nouveaux champs si ajoutés
        // 'approved_at' => 'datetime',
        // 'blocked_at' => 'datetime',
        // 'cin_verified_at' => 'datetime',
    ];

    // --- Relations ---
    public function objets() { return $this->hasMany(Objet::class, 'proprietaire_id'); }
    public function annonces() { return $this->hasMany(Annonce::class, 'proprietaire_id'); }
    public function reservations() { return $this->hasMany(Reservation::class, 'client_id'); }
    public function evaluationsDonnees() { return $this->hasMany(Evaluation::class, 'evaluateur_id'); }
    public function evaluationsRecues() { return $this->hasMany(Evaluation::class, 'evalue_id'); }
    public function notifications() { return $this->hasMany(AppNotification::class, 'utilisateur_id'); }
    public function reclamations() { return $this->hasMany(Reclamation::class, 'utilisateur_id'); }

    // --- Méthodes pour Auth ---
    public function getAuthPassword() { return $this->mot_de_passe; }
    public function getAuthPasswordName() { return 'mot_de_passe'; }

    // --- Accessors ---
    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->nom . ' ' . $this->prenom);
    }

    /**
     * Get the URL to the user's profile avatar.
     *
     * @return string
     */
    public function getAvatarUrlAttribute(): string
    {
        // Assurez-vous que le disk 'public' est configuré dans config/filesystems.php
        // et que le lien symbolique a été créé (php artisan storage:link)
        if ($this->image_profile && Storage::disk('public')->exists($this->image_profile)) {
            return Storage::disk('public')->url($this->image_profile);
        }
        // Placeholder généré via ui-avatars.com
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name ?: $this->email) . '&background=random&color=fff&size=40';
        // Ou une image locale: return asset('images/default-avatar.png');
    }

    // TODO: Ajouter des méthodes helper si vous ajoutez des champs de statut
    // Exemple:
    // public function isBlocked(): bool {
    //     return !is_null($this->blocked_at);
    // }
    // public function isCinVerified(): bool {
    //     return !is_null($this->cin_verified_at);
    // }
}