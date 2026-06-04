<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenererPreventif extends Command
{
    /**
     * Le nom de la commande appelé par ton bouton web
     */
    protected $signature = 'app:generer-preventif';

    /**
     * La description de la commande
     */
    protected $description = 'Scanne les équipements et les types ERP soumis à contrôle pour générer la maintenance préventive';

    /**
     * Exécution du moteur
     */
    public function handle()
    {
        $this->info('🚀 Lancement du moteur de maintenance préventive (Équipements & ERP)...');
        $interventionsCreees = 0;

        // =========================================================================
        // VOLET 1 : DOSSIERS PRÉVENTIFS PAR ÉQUIPEMENT (Table : soumis_a_controle)
        // =========================================================================
        $controlesEquipements = DB::table('soumis_a_controle')
            ->join('controle_reglementaire', 'soumis_a_controle.id_controle', '=', 'controle_reglementaire.id_controle')
            ->join('equipement', 'soumis_a_controle.id_equipement', '=', 'equipement.id_equipement')
            ->select(
                'soumis_a_controle.*',
                'controle_reglementaire.designation',
                'controle_reglementaire.frequence_mois',
                'equipement.nom_equipement',
                'equipement.id_local',
                'equipement.id_famille' // ✅ On récupère l'id_famille au lieu de id_cat qui n'existe pas dans controle
            )
            ->get();

        foreach ($controlesEquipements as $liaison) {
            if (empty($liaison->date_controle))
                continue;

            $dateProchaineEcheance = Carbon::parse($liaison->date_controle)->addMonths($liaison->frequence_mois ?? 12);

            if (Carbon::now()->greaterThanOrEqualTo($dateProchaineEcheance)) {
                // Vérification si un préventif identique est déjà ouvert et non fermé
                $existeDeja = DB::table('intervention')
                    ->join('intervention_equipement', 'intervention.id_int', '=', 'intervention_equipement.id_int')
                    ->where('intervention_equipement.id_equipement', $liaison->id_equipement)
                    ->where('intervention.statut_global', 'En cours')
                    ->where('intervention.type_intervention', 'LIKE', '[PRÉVENTIF EQUIP]%')
                    ->exists();

                if (!$existeDeja) {
                    DB::transaction(function () use ($liaison, $dateProchaineEcheance, &$interventionsCreees) {

                        // Dynamique : On cherche une catégorie correspondante ou par défaut la première existante
                        $idCatAlternative = DB::table('categorie')->value('id_cat') ?? 1;

                        $idNewInt = DB::table('intervention')->insertGetId([
                            'date_ouverture' => now(),
                            'type_intervention' => '[PRÉVENTIF EQUIP] ' . substr($liaison->designation, 0, 120),
                            'statut_global' => 'En cours',
                            'description' => "⚠️ MAINTENANCE PRÉVENTIVE SUR ÉQUIPEMENT\n" .
                                "Échéance réglementaire dépassée depuis le : " . $dateProchaineEcheance->format('d/m/Y') . ".\n" .
                                "Vérification requise sur l'appareil : " . $liaison->nom_equipement,
                            'id_cat' => $idCatAlternative, // ✅ Corrigé ici
                            'id_local' => $liaison->id_local ?: null,
                            'code_budget' => 'PE',
                            'date_cloture' => null,
                            'id_compteur' => null,
                            'id_troncon' => null,
                            'id_tiers' => null,
                            'id_contrat' => null,
                            'id_user_demandeur' => 1,
                            'id_service' => null,
                            'id_action' => null,
                            'Autre' => null
                        ], 'id_int');

                        DB::table('intervention_equipement')->insert([
                            'id_int' => $idNewInt,
                            'id_equipement' => $liaison->id_equipement
                        ]);
                        $interventionsCreees++;
                    });
                }
            }
        }

        // =========================================================================
        // VOLET 2 : DOSSIERS PRÉVENTIFS PAR BÂTIMENT ERP (Table : type_erp_controle)
        // =========================================================================
        $controlesErp = DB::table('type_erp_controle')
            ->join('controle_reglementaire', 'type_erp_controle.id_controle', '=', 'controle_reglementaire.id_controle')
            ->join('type_erp', 'type_erp_controle.id_type_erp', '=', 'type_erp.id_type_erp')
            ->select(
                'type_erp_controle.*',
                'controle_reglementaire.designation',
                'controle_reglementaire.frequence_mois',
                'type_erp.type_erp',
                'type_erp.categorie_erp'
            )
            ->get();

        foreach ($controlesErp as $liaisonErp) {
            if (empty($liaisonErp->date_controle))
                continue;

            $dateProchaineEcheanceErp = Carbon::parse($liaisonErp->date_controle)->addMonths($liaisonErp->frequence_mois ?? 12);

            if (Carbon::now()->greaterThanOrEqualTo($dateProchaineEcheanceErp)) {

                $batimentsLies = DB::table('batiment')
                    ->where('id_type_erp', $liaisonErp->id_type_erp)
                    ->get();

                foreach ($batimentsLies as $bat) {
                    $idCatAlternativeErp = DB::table('categorie')->value('id_cat') ?? 1;

                    $existeDejaSurBat = DB::table('intervention')
                        ->where('id_cat', $idCatAlternativeErp) // ✅ Corrigé ici
                        ->where('statut_global', 'En cours')
                        ->where('type_intervention', 'LIKE', '[PRÉVENTIF ERP]%')
                        ->where('description', 'LIKE', '%🏛️ Bâtiment : ' . $bat->nom_bat . '%')
                        ->exists();

                    if (!$existeDejaSurBat) {
                        DB::transaction(function () use ($liaisonErp, $bat, $dateProchaineEcheanceErp, $idCatAlternativeErp, &$interventionsCreees) {
                            DB::table('intervention')->insert([
                                'date_ouverture' => now(),
                                'type_intervention' => '[PRÉVENTIF ERP] ' . substr($liaisonErp->designation, 0, 120),
                                'statut_global' => 'En cours',
                                'description' => "🚨 VÉRIFICATION RÉGLEMENTAIRE OBLIGATOIRE ERP\n" .
                                    "Échéance du contrôle de catégorie dépassée depuis le : " . $dateProchaineEcheanceErp->format('d/m/Y') . ".\n" .
                                    "🏛️ Bâtiment : " . $bat->nom_bat . " (Classé ERP Catégorie " . $liaisonErp->categorie_erp . " - Type " . $liaisonErp->type_erp . ")",
                                'id_cat' => $idCatAlternativeErp, // ✅ Corrigé ici
                                'id_local' => null,
                                'code_budget' => 'PV',
                                'date_cloture' => null,
                                'id_compteur' => null,
                                'id_troncon' => null,
                                'id_tiers' => null,
                                'id_contrat' => null,
                                'id_user_demandeur' => 1,
                                'id_service' => null,
                                'id_action' => null,
                                'Autre' => null
                            ]);
                            $interventionsCreees++;
                        });
                    }
                }
            }
        }

        $this->info("✔️ Fin du scan préventif global. {$interventionsCreees} ordres de travaux générés.");
    }
}