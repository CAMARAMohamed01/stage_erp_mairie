<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batiment extends Model
{
    use HasFactory;

    protected $table = 'batiment';
    protected $primaryKey = 'id_batiment';
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
}