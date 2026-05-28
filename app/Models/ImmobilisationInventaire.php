<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImmobilisationInventaire extends Model
{
    //
    protected $table = "immobilisation_inventaire_";
    protected $primaryKey = 'id_immo';
    public $timestamps = false;
    protected $guarded = [];
}