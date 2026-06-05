<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunePartenaire extends Model
{
    //
    protected $table = 'commune_partenaire';
    protected $primaryKey = 'id_commune';
    public $timestamps = false;
    protected $guarded = [];
}