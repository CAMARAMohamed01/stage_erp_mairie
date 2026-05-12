<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    use HasFactory;

    protected $table = 'utilisateur';
    protected $primaryKey = 'id_user';
    protected $guarded = [];
    public $timestamps = false;

    // Les agents pourront plus tard être reliés à leurs Interventions ou Signalements !
}