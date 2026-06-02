<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function show()
    {
        // On récupère les infos complètes de l'utilisateur connecté
        $user = DB::table('utilisateur')
            ->leftJoin('service_mairie', 'utilisateur.id_service', '=', 'service_mairie.id_service')
            ->where('id_user', Auth::id())
            ->select('utilisateur.*', 'service_mairie.nom_service')
            ->first();

        return view('profil.show', compact('user'));
    }

    public function update(Request $request)
    {
        $idUser = Auth::id();

        $request->validate([
            'nom_user' => 'required|string|max:50',
            'prenom_user' => 'required|string|max:50',
            'emailpro' => 'nullable|email|max:80|unique:utilisateur,emailpro,' . $idUser . ',id_user',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $updateData = [
            'nom_user' => $request->nom_user,
            'prenom_user' => $request->prenom_user,
            'emailpro' => $request->emailpro,
        ];

        // On ne met à jour le mot de passe que si l'utilisateur en a saisi un nouveau
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('utilisateur')->where('id_user', $idUser)->update($updateData);

        return redirect()->route('profil.show')->with('success', 'Vos informations personnelles ont été mises à jour !');
    }
}