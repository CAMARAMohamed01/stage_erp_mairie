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
    public $timestamps = false;
    // Une catégorie peut regrouper de nombreux actions
    public function actions()
    {
        return $this->hasMany(action::class, 'id_cat', 'id_cat');
    }
}