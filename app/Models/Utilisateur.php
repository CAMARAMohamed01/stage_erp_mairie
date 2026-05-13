<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class Utilisateur extends Authenticatable
{
    use Notifiable;

    protected $table = 'utilisateur';
    protected $primaryKey = 'id_user';
    public $timestamps = false; // Indispensable puisque tu n'as pas created_at / updated_at

    protected $fillable = [
        'initiales',
        'nom_user',
        'prenom_user',
        'role_appli',
        'emailpro',
        'password',
        'id_service',
        'id_profil'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Tu peux garder tes relations (hasMany, belongsTo...) en dessous si tu en as déjà créé.
    public function service()
    {
        return $this->belongsTo(ServiceMairie::class, 'id_service', 'id_service');
    }

    public function profil()
    {
        return $this->belongsTo(ProfilAcces::class, 'id_profil', 'id_profil');
    }
}