<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
// N'oubliez pas d'importer la façade DB pour les requêtes directes
use Illuminate\Support\Facades\DB;
// models utilisateur
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('check-permission', function ($user, string $nomModule, string $typeDroit) {

            // Sécurité absolue : l'Administrateur système garde un accès total
            if ($user->role_appli === 'Administrateur') {
                return true;
            }

            // Mappage avec tes colonnes PostgreSQL
            $colonneDroit = match ($typeDroit) {
                'lecture' => 'droit_lecture',
                'ecriture' => 'droit_ecriture',
                'suppression' => 'droit_suppression',
                'validation' => 'droit_validation',
                default => null,
            };

            if (!$colonneDroit) {
                return false;
            }

            // Requête directe : on cherche le droit pour CET utilisateur et CE module
            $autorisation = DB::table('habilitation')
                ->join('module_logiciel', 'habilitation.id_module', '=', 'module_logiciel.id_module')
                ->where('habilitation.id_user', $user->id_user) // Liaison directe à l'agent
                ->where('module_logiciel.nom_module', $nomModule)
                ->value($colonneDroit);

            return (bool) $autorisation;
        });
    }
}