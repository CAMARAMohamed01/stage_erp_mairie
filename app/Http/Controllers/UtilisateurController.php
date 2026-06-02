<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
// On importe la classe Str pour manipuler les chaînes de caractères
use Illuminate\Support\Str;

class UtilisateurController extends Controller
{
    public function index()
    {
        $utilisateurs = DB::table('utilisateur')
            ->leftJoin('service_mairie', 'utilisateur.id_service', '=', 'service_mairie.id_service')
            ->select('utilisateur.*', 'service_mairie.nom_service')
            ->get();

        return view('admin.utilisateurs.index', compact('utilisateurs'));
    }

    public function create()
    {
        // On récupère tous les services pour le menu déroulant d'affectation
        $services = DB::table('service_mairie')->get();
        return view('admin.utilisateurs.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_user' => 'required|string|max:50',
            'prenom_user' => 'required|string|max:50',
            'initiales' => 'nullable|string|max:5',
            'role_appli' => 'required|string|max:50',
            'emailpro' => 'nullable|email|max:80|unique:utilisateur,emailpro',
            'password' => 'required|string|min:6|confirmed',
            'id_service' => 'nullable|exists:service_mairie,id_service',
        ]);

        DB::table('utilisateur')->insert([
            'nom_user' => $request->nom_user,
            'prenom_user' => $request->prenom_user,
            'initiales' => Str::upper($request->initiales),
            'role_appli' => $request->role_appli,
            'emailpro' => $request->emailpro,
            'password' => Hash::make($request->password), // Sécurité !
            'id_service' => $request->id_service,
        ]);

        return redirect()->route('utilisateurs.index')->with('success', 'L\'utilisateur a été créé et affecté avec succès !');
    }
    public function edit($id)
    {
        // Récupère l'utilisateur ciblé
        $user = DB::table('utilisateur')->where('id_user', $id)->first();

        if (!$user) {
            return redirect()->route('utilisateurs.index')->with('error', 'Agent introuvable.');
        }

        // Récupère les services pour le menu déroulant
        $services = DB::table('service_mairie')->get();

        return view('admin.utilisateurs.edit', compact('user', 'services'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom_user' => 'required|string|max:50',
            'prenom_user' => 'required|string|max:50',
            'initiales' => 'nullable|string|max:5',
            'role_appli' => 'required|string|max:50',
            'emailpro' => 'nullable|email|max:80|unique:utilisateur,emailpro,' . $id . ',id_user',
            'password' => 'nullable|string|min:6|confirmed', // Mot de passe optionnel à la modification
            'id_service' => 'nullable|exists:service_mairie,id_service',
        ]);

        $updateData = [
            'nom_user' => $request->nom_user,
            'prenom_user' => $request->prenom_user,
            'initiales' => Str::upper($request->initiales),
            'role_appli' => $request->role_appli,
            'emailpro' => $request->emailpro,
            'id_service' => $request->id_service,
        ];

        // On ne change le mot de passe que s'il est renseigné
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('utilisateur')->where('id_user', $id)->update($updateData);

        return redirect()->route('utilisateurs.index')->with('success', 'Les informations de l\'agent ont été modifiées.');
    }

    public function destroy($id)
    {
        // Éviter qu'un administrateur ne supprime son propre compte par erreur
        if ($id == auth()->id()) {
            return redirect()->route('utilisateurs.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Suppression de l'utilisateur
        DB::table('utilisateur')->where('id_user', $id)->delete();

        return redirect()->route('utilisateurs.index')->with('success', 'L\'agent a été retiré du registre.');
    }
}