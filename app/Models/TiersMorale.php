<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiersMorale extends Model
{
    use HasFactory;

    protected $table = 'tiers_morale';
    protected $primaryKey = 'id_tiers';

    // Pareil ici, pas d'auto-incrémentation !
    public $incrementing = false;

    protected $guarded = [];

    // Lien pour remonter vers la table mère
    public function tiersParent()
    {
        return $this->belongsTo(Tiers::class, 'id_tiers', 'id_tiers');
    }
}