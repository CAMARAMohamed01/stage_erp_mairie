<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiersPhysique extends Model
{
    use HasFactory;

    protected $table = 'tiers_physique';
    protected $primaryKey = 'id_tiers';

    // On dit à Laravel que cette clé ne s'auto-incrémente pas 
    // (car elle est générée par la table mère 'tiers')
    public $incrementing = false;

    protected $guarded = [];

    // Lien pour remonter vers la table mère
    public function tiersParent()
    {
        return $this->belongsTo(Tiers::class, 'id_tiers', 'id_tiers');
    }
}