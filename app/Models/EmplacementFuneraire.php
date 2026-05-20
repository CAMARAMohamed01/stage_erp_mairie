<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmplacementFuneraire extends Model
{
    use HasFactory;

    protected $table = 'emplacement_funeraire';
    protected $primaryKey = 'id_emplacement';
    public $timestamps = false; // Car il n'y a pas de created_at/updated_at dans ton DDL
    protected $guarded = [];

    // Relation avec le cimetière (Lieu public)
    public function lieu()
    {
        return $this->belongsTo(LieuPublic::class, 'id_lieu', 'id_lieu');
    }

    // Relation future vers la concession
    public function concessions()
    {
        return $this->hasMany(ConcessionCimetiere::class, 'id_emplacement', 'id_emplacement');
    }
}