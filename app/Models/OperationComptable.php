<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationComptable extends Model
{
    use HasFactory;

    protected $table = 'operation_comptable';
    protected $primaryKey = 'id_operation'; // (Adaptez si votre PK s'appelle différemment, ex: id_op)
    protected $guarded = [];

    // L'opération appartient à un projet global
    public function projet()
    {
        return $this->belongsTo(Projet::class, 'id_projet', 'id_projet');
    }
    public function decisions()
    {
        return $this->belongsToMany(DecisionAdministratif::class, 'acte_operation', 'id_operation', 'id_decision');
    }
}