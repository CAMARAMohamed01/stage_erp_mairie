<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcelle extends Model
{
    use HasFactory;

    protected $table = 'parcelle';
    protected $primaryKey = 'id_parcelle';
    public $timestamps = false;
    protected $guarded = [];

    // --- RELATIONS DE BASE ---
    public function lieuDit()
    {
        return $this->belongsTo(LieuDit::class, 'id_lieu_dit', 'id_lieu_dit');
    }

    public function batiments()
    {
        return $this->hasMany(Batiment::class, 'id_parcelle', 'id_parcelle');
    }

    public function lieuxPublics()
    {
        return $this->hasMany(LieuPublic::class, 'id_parcelle', 'id_parcelle');
    }

    public function immobilisation()
    {
        return $this->belongsTo(ImmobilisationInventaire::class, 'id_immo', 'id_immo');
    }

    // --- RELATIONS VIA TABLES PIVOTS (PROPRIO & URBANISME) ---
    public function proprietaires()
    {
        return $this->belongsToMany(Tiers::class, 'proprio_parcelle', 'id_parcelle', 'id_tiers')
            ->withPivot('date_acquisition', 'date_vente', 'pourcentage_part', 'prix_parcelle');
    }

    public function dossiersUrba()
    {
        return $this->belongsToMany(DossierUrba::class, 'dossier_parcelle', 'id_parcelle', 'id_dossier');
    }
}