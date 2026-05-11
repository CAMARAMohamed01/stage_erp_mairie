<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $table = 'categorie';
    protected $primaryKey = 'id_cat';
    protected $guarded = [];

    // Une catégorie peut regrouper de nombreux signalements
    public function signalements()
    {
        return $this->hasMany(Signalement::class, 'id_cat', 'id_cat');
    }
}