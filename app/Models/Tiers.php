<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiers extends Model
{
    use HasFactory;

    protected $table = 'tiers';
    protected $primaryKey = 'id_tiers';
    protected $guarded = [];

    // Le Tiers possède une adresse
    public function adresse()
    {
        return $this->belongsTo(Adresse::class, 'id_adresse', 'id_adresse');
    }

    // Liens vers ses "enfants" potentiels (Héritage)
    public function physique()
    {
        return $this->hasOne(TiersPhysique::class, 'id_tiers', 'id_tiers');
    }

    public function morale()
    {
        return $this->hasOne(TiersMorale::class, 'id_tiers', 'id_tiers');
    }
}