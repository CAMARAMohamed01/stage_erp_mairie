<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secteur extends Model
{
    use HasFactory;

    // 1. On précise le nom de la table
    protected $table = 'secteur';

    // 2. On précise la clé primaire
    protected $primaryKey = 'id_secteur';

    // 3. On autorise l'insertion en masse (très utile pour l'import Excel !)
    protected $guarded = [];

    // 4. LA RELATION : Un secteur possède PLUSIEURS zones
    public function zones()
    {
        return $this->hasMany(Zone::class, 'id_secteur', 'id_secteur');
    }
}