<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeErp extends Model
{
    protected $table = 'type_erp';
    protected $primaryKey = 'id_type_erp';
    public $timestamps = false;

    protected $fillable = [
        'reglementation_applicable',
        'public_cible',
        'categorie_erp',
        'type_erp'
    ];

    // La relation Many-to-Many vers les contrôles
    public function controles()
    {
        return $this->belongsToMany(ControleReglementaire::class, 'type_erp_controle', 'id_type_erp', 'id_controle');
    }
}