<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationEquipement extends Model
{
    use HasFactory;

    protected $table = 'location_equipement';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'date_debut_utilisation' => 'date',
        'date_fin_utilisation' => 'date',
        'date_modification' => 'date',
    ];

    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat');
    }

    public function equipement()
    {
        return $this->belongsTo(Equipement::class, 'id_equipement');
    }

    public function decision()
    {
        return $this->belongsTo(DecisionAdministratif::class, 'id_decision');
    }
}