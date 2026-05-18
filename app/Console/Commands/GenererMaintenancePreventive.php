<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\Console\Input\InputOption;

#[Signature('app:generer-maintenance-preventive')]
#[Description('Command description')]
class GenererMaintenancePreventive extends Command
{
    // La commande textuelle à taper dans le terminal
    protected $signature = 'app:generer-preventif';

    // La description de la commande
    protected $description = 'Analyse les contrôles réglementaires et génère automatiquement les futures interventions de maintenance préventive';

    public function handle()
    {
        $this->info('🚀 Démarrage de la génération de la maintenance préventive...');

        // 1. Récupérer tous les contrôles réglementaires actifs qui ont une fréquence définie
        // Adapte les noms des colonnes si elles diffèrent légèrement dans ton DDL (ex: frequence_mois, id_lieu...)
        $controles = DB::table('controle_reglementaire')
            ->whereNotNull('frequence_mois')
            ->get();

        $compteur = 0;

        foreach ($controles as $controle) {
            // Déterminer la date de point de départ (soit la date du dernier contrôle, soit aujourd'hui)
            $dateDernierControle = $controle->date_dernier_controle
                ? Carbon::parse($controle->date_dernier_controle)
                : Carbon::now();

            // Calcul de la prochaine échéance théorique
            $prochaineEcheance = $dateDernierControle->addMonths($controle->frequence_mois);

            // Optionnel : On ne génère les interventions que si l'échéance arrive bientôt (ex: dans les 3 prochains mois)
            if ($prochaineEcheance->isAfter(Carbon::now()->addMonths(3))) {
                continue; // Trop loin dans le futur, on passe au contrôle suivant
            }

            // 2. Éviter les doublons : on vérifie si une intervention préventive existe déjà pour ce contrôle à cette date
            // On considère qu'une intervention est un doublon si elle cible le même contrôle et le même lieu le même mois
            $doublon = DB::table('intervention')
                ->where('id_controle', $controle->id_controle)
                ->where('id_lieu', $controle->id_lieu)
                ->whereMonth('date_prevue', $prochaineEcheance->month)
                ->whereYear('date_prevue', $prochaineEcheance->year)
                ->exists();

            if (!$doublon) {
                // 3. Insertion de la nouvelle intervention automatisée
                DB::table('intervention')->insert([
                    'designation' => '🛠️ [PRÉVENTIF] ' . $controle->designation,
                    'description' => 'Généré automatiquement par le système de maintenance préventive. Libellé du contrôle : ' . $controle->description,
                    'statut_global' => 'En attente', // Reste à attribuer à un technicien
                    'date_creation' => Carbon::now(),
                    'date_prevue' => $prochaineEcheance,
                    'id_controle' => $controle->id_controle,
                    'id_lieu' => $controle->id_lieu,
                    'id_type_erp' => $controle->id_type_erp,
                    'id_categorie' => $controle->id_categorie ?? null, // Si tu as une catégorie d'intervention
                ]);

                // Optionnel : Mettre à jour la date prévisionnelle dans la table contrôle pour le suivi
                DB::table('controle_reglementaire')
                    ->where('id_controle', $controle->id_controle)
                    ->update(['date_prochain_controle' => $prochaineEcheance]);

                $compteur++;
            }
        }

        $this->info("✅ Opération terminée. $compteur nouvelles interventions préventives ont été générées !");
        return Command::SUCCESS;
    }
}