<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    use HasFactory;

    protected $table = 'intervention';
    protected $primaryKey = 'id_intervention';
    protected $guarded = [];

    // Une intervention peut faire suite à un signalement
    public function signalement()
    {
        return $this->belongsTo(Signalement::class, 'id_sig', 'id_sig');
    }

    // L'agent ou l'équipe assignée à l'intervention
    public function responsable()
    {
        return $this->belongsTo(Utilisateur::class, 'id_user', 'id_user');
    }
}