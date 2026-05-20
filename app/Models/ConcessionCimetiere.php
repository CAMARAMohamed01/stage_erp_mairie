<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConcessionCimetiere extends Model
{
    protected $table = 'concession_cimetiere';
    protected $primaryKey = 'id_concession';
    public $timestamps = false;
    protected $guarded = [];

    // Relation vers l'emplacement physique
    public function emplacement()
    {
        return $this->belongsTo(EmplacementFuneraire::class, 'id_emplacement');
    }

    // Relation vers le contrat (pour les dates, le prix et le titulaire)
    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat');
    }

    // Relation Many-to-Many avec les défunts (Tiers physiques)
    public function defunts()
    {
        return $this->belongsToMany(
            TiersPhysique::class,
            'defunt_concession',
            'id_concession',
            'id_tiers'
        );
    }
}