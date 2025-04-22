<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Ajouter pour l'accessor

class Image extends Model
{
    use HasFactory;

    protected $table = 'images';

    protected $fillable = [
        'url', // Suppose que 'url' stocke le chemin relatif dans storage/app/public
        'objet_id',
    ];

    // Relation OK
    public function objet()
    {
        return $this->belongsTo(Objet::class);
    }

    // Ajout: Accessor pour obtenir l'URL publique complète
    public function getPublicUrlAttribute(): ?string
    {
        if ($this->url && Storage::disk('public')->exists($this->url)) {
            return Storage::disk('public')->url($this->url);
        }
        // Retourner une image placeholder ou null
        return asset('images/placeholder.jpg'); // Assurez-vous que ce placeholder existe
        // Ou return null;
    }
}