<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $table = 'Zone';
    protected $primaryKey = 'id_zone';
    protected $guarded = [];

    // LA RELATION INVERSE : Une zone APPARTIENT À un secteur
    public function secteur()
    {
        return $this->belongsTo(Secteur::class, 'id_secteur', 'id_secteur');
    }
}