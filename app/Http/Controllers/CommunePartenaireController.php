<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunePartenaireController extends Controller
{
    public function index()
    {
        $communes = DB::table('commune_partenaire')->orderBy('nom_commune')->paginate(15);
        return view('communes.index', compact('communes'));
    }

    public function create()
    {
        return view('communes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_commune' => 'required|string|max:80|unique:commune_partenaire,nom_commune',
            'code_postal' => 'nullable|string|max:5',
            'siret_mairie' => 'nullable|string|max:14',
            'email_contact' => 'nullable|email|max:100',
        ], [
            'nom_commune.unique' => 'Cette commune est déjà enregistrée dans le système.',
        ]);

        DB::table('commune_partenaire')->insert($validated);

        // Si l'utilisateur a été redirigé depuis un ouvrage, on pourrait le renvoyer dessus, 
        // mais par défaut on le renvoie sur la liste des communes avec un message.
        return redirect()->route('communes.index')->with('success', 'La commune partenaire a été ajoutée.');
    }
}