<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batiment extends Model
{
    use HasFactory;

    protected $table = 'batiment';
    protected $primaryKey = 'id_batiment';
    public $timestamps = false;
    protected $guarded = [];
    public function contratsAdministratifs()
    {
        return $this->belongsToMany(\App\Models\Contrat::class, 'contrat_batiment', 'id_batiment', 'id_contrat');
    }

    // Un bâtiment est posé sur une parcelle et situé à une adresse
    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class, 'id_parcelle', 'id_parcelle');
    }

    public function adresse()
    {
        return $this->belongsTo(Adresse::class, 'id_adresse', 'id_adresse');
    }
    public function documents()
    {
        return $this->hasMany(Document::class, 'id_batiment', 'id_batiment');
    }

    // Un bâtiment peut avoir plusieurs locaux
    public function locaux()
    {
        return $this->hasMany(Local::class, 'id_batiment', 'id_batiment');
    }

    // Un bâtiment est souvent lié à une immobilisation comptable
    public function immobilisation()
    {
        return $this->belongsTo(ImmobilisationInventaire::class, 'id_immo', 'id_immo');
    }
}