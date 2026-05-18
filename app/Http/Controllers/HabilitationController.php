<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HabilitationController extends Controller
{
    public function index(Request $request)
    {
        // 1. On récupère les utilisateurs (les agents) et les modules
        $utilisateurs = DB::table('utilisateur')->orderBy('nom_user')->get();
        $modules = DB::table('module_logiciel')->orderBy('nom_module')->get();

        // 2. Récupérer l'agent actuellement sélectionné (par défaut le premier de la liste)
        $id_user_selectionne = $request->query('id_user', $utilisateurs->first()->id_user ?? null);

        // 3. Charger ses habilitations spécifiques
        $habilitations = [];
        if ($id_user_selectionne) {
            $rawHabilitations = DB::table('habilitation')
                ->where('id_user', $id_user_selectionne)
                ->get();

            foreach ($rawHabilitations as $hab) {
                $habilitations[$hab->id_module] = $hab;
            }
        }

        return view('admin.habilitations.index', compact('utilisateurs', 'modules', 'id_user_selectionne', 'habilitations'));
    }

    public function update(Request $request)
    {
        $id_user = $request->input('id_user');
        $droits = $request->input('droits', []);

        $modules = DB::table('module_logiciel')->get();

        DB::transaction(function () use ($id_user, $modules, $droits) {
            foreach ($modules as $module) {
                $lecture = isset($droits[$module->id_module]['lecture']);
                $ecriture = isset($droits[$module->id_module]['ecriture']);
                $suppression = isset($droits[$module->id_module]['suppression']);
                $validation = isset($droits[$module->id_module]['validation']);

                // Contrainte de clé composite (id_user, id_module)
                DB::table('habilitation')->updateOrInsert(
                    ['id_user' => $id_user, 'id_module' => $module->id_module],
                    [
                        'droit_lecture' => $lecture,
                        'droit_ecriture' => $ecriture,
                        'droit_suppression' => $suppression,
                        'droit_validation' => $validation,
                        'id_user' => $id_user // Assure la cohérence de la FK
                    ]
                );
            }
        });

        return redirect()->route('admin.habilitations.index', ['id_user' => $id_user])
            ->with('success', 'Les permissions de l\'agent ont été mises à jour.');
    }
}