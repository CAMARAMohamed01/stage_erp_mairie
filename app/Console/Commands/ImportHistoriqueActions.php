<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
class ImportHistoriqueActions extends Command
{
    // 💡 AJOUT : L'option --rollback pour pouvoir faire machine arrière !
    protected $signature = 'import:historique {fichier : Chemin vers le CSV} {--rollback : Supprime les données de ce fichier}';
    protected $description = 'ETL : Importe l\'historique, croise les lieux-dits/publics, avec option de rollback.';

    public function handle()
    {
        $fichier = $this->argument('fichier');
        if (!file_exists($fichier)) {
            $this->error("Fichier introuvable !");
            return 1;
        }

        $file = fopen($fichier, 'r');
        $header = fgetcsv($file, 1000, ';'); // Ignorer l'en-tête

        // ==========================================
        // 🔄 MARCHE ARRIÈRE (ROLLBACK)
        // ==========================================
        if ($this->option('rollback')) {
            $this->warn("⚠️ DÉMARRAGE DU ROLLBACK...");
            $ids_a_supprimer = [];

            // On récolte tous les index (ID) du fichier Excel
            while (($data = fgetcsv($file, 1000, ';')) !== FALSE) {
                $index_brut = preg_replace('/[^0-9]/', '', $data[0]);
                $index = (int) $index_brut;
                if ($index === 0) {
                    continue;
                }
                if (!empty($index)) {
                    $ids_a_supprimer[] = $index;
                }
            }
            fclose($file);

            if (empty($ids_a_supprimer)) {
                $this->info("Aucun ID trouvé dans le fichier.");
                return 0;
            }

            DB::beginTransaction();
            try {
                // 1. On nettoie d'abord TOUTES les tables pivots/enfants liées à l'intervention
                DB::table('suivi_action')->whereIn('id_int', $ids_a_supprimer)->delete();
                DB::table('intervention_espace')->whereIn('id_int', $ids_a_supprimer)->delete();
                DB::table('equipe_intervention')->whereIn('id_int', $ids_a_supprimer)->delete(); // <-- LA CORRECTION POUR TON CRASH
                DB::table('intervention_equipement')->whereIn('id_int', $ids_a_supprimer)->delete();
                //DB::table('intervention_lieu')->whereIn('id_int', $ids_a_supprimer)->delete();
                DB::table('achat_materiel_consommable')->whereIn('id_int', $ids_a_supprimer)->delete();
                DB::table('decision_commission')->whereIn('id_int', $ids_a_supprimer)->delete();
                DB::table('document')->whereIn('id_int', $ids_a_supprimer)->delete();

                // 2. Maintenant que l'intervention n'est plus liée nulle part, on peut la supprimer
                DB::table('intervention')->whereIn('id_int', $ids_a_supprimer)->delete();

                // 3. Enfin, on supprime l'action (le signalement d'origine)
                DB::table('action')->whereIn('id_action', $ids_a_supprimer)->delete();

                DB::commit();
                $this->info("✅ ROLLBACK RÉUSSI : Les données de ce fichier ont été effacées de la BDD.");
                return 0;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Erreur lors du rollback : " . $e->getMessage());
                return 1;
            }
        }
        // ==========================================
        // 📥 IMPORTATION (ETL CLASSIQUE)
        // ==========================================
        $this->info("Début de l'importation de l'historique...");
        $compteur = 0;

        DB::beginTransaction();

        try {
            while (($data = fgetcsv($file, 1000, ';')) !== FALSE) {
                $data = array_map(function ($value) {
                    return mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
                }, $data);

                $index_brut = preg_replace('/[^0-9]/', '', $data[0]);
                $index = (int) $index_brut;
                if ($index === 0)
                    continue;

                $date_ouverture_raw = trim($data[1]);
                $initiales = trim($data[2]);
                $code_budget = trim($data[3]);
                $nom_lieu_dit_excel = trim($data[4]); // Colonne E
                $nom_bat_mélangé = trim($data[7]);    // Colonne H (Le fameux mélange)
                $categorie_raw = trim($data[9]);      // Colonne J
                $priorite = trim($data[10]);
                $description_action = trim($data[11]);
                $couts_raw = trim($data[12]);
                $statut_global = trim($data[13]);
                $date_cloture_raw = trim($data[14]);
                $suivis = [trim($data[15] ?? ''), trim($data[16] ?? '')];

                if (empty($index) || empty($date_ouverture_raw))
                    continue;

                // 1. TRANSFORM : Dates et Règles Métiers
                $date_ouverture = Carbon::createFromFormat('d/m/y', date('d/m/y', strtotime(str_replace('/', '-', $date_ouverture_raw))));
                $date_cloture = empty($date_cloture_raw) ? null : Carbon::createFromFormat('d/m/y', date('d/m/y', strtotime(str_replace('/', '-', $date_cloture_raw))));

                if (!$date_cloture && $date_ouverture->diffInYears(now()) >= 2) {
                    $statut_global = 'Terminé';
                    $statut_action = 'Terminé';
                } else {
                    $statut_action = $date_cloture ? 'Terminé' : 'En cours';
                }

                $cout = floatval(preg_replace('/[^0-9,.]/', '', str_replace(',', '.', $couts_raw)));

                // 2. TRANSFORM : Utilisateur et Catégorie
                $user = DB::table('utilisateur')->where('initiales', strtoupper($initiales))->first();
                $id_user = $user ? $user->id_user : null;

                $cat_libelle = ucfirst(strtolower($categorie_raw));
                if (empty($cat_libelle))
                    $cat_libelle = "Général";

                $categorie = DB::table('categorie')->where('libelle', 'ILIKE', $cat_libelle)->first();
                $id_cat = $categorie ? $categorie->id_cat : DB::table('categorie')->insertGetId(['libelle' => $cat_libelle], 'id_cat');

                // ==============================================================
                // 3. TRANSFORM : LE DÉTECTIVE (LOCAL / BATIMENT / LIEU)
                // ==============================================================
                $id_lieu = null;
                $id_batiment = null;
                $id_local = null;

                $lieu_recherche = !empty($nom_bat_mélangé) ? $nom_bat_mélangé : $nom_lieu_dit_excel;

                if (!empty($lieu_recherche)) {
                    // ÉTAPE A : Existe-t-il dans la table des LOCAUX ?
                    $local = DB::table('local_')->where('nom_local', 'ILIKE', $lieu_recherche)->first();

                    if ($local) {
                        $id_local = $local->id_local;
                        $id_batiment = $local->id_batiment; // S'il a un bâtiment rattaché, on le prend
                        $id_lieu = $local->id_lieu;         // S'il a un lieu public rattaché, on le prend
                    } else {
                        // ÉTAPE B : Existe-t-il dans la table des BÂTIMENTS ?
                        $batiment = DB::table('batiment')->where('nom_bat', 'ILIKE', $lieu_recherche)->first();

                        if ($batiment) {
                            $id_batiment = $batiment->id_batiment;
                        } else {
                            // ÉTAPE C : Existe-t-il dans la table des LIEUX PUBLICS ?
                            $lieuPublic = DB::table('lieux_publics')->where('nom_lieu', 'ILIKE', $lieu_recherche)->first();

                            if ($lieuPublic) {
                                $id_lieu = $lieuPublic->id_lieu;
                            } else {
                                // ÉTAPE D : Existe-t-il dans la table des LIEUX DITS ?
                                $lieuDit = DB::table('lieu_dit')->where('nom_lieu_dit', 'ILIKE', $lieu_recherche)->first();

                                if ($lieuDit) {
                                    $id_lieu = DB::table('lieux_publics')->insertGetId([
                                        'nom_lieu' => $lieuDit->nom_lieu_dit,
                                        'typologie_lieu' => 'Lieu Dit (Import)'
                                    ], 'id_lieu');
                                } else {
                                    // ÉTAPE E : TOTALEMENT INCONNU. Application de la règle "Salle"
                                    if (Str::startsWith(strtolower($lieu_recherche), 'salle')) {
                                        // On crée un Local !
                                        $id_local = DB::table('local_')->insertGetId([
                                            'nom_local' => $lieu_recherche,
                                            'statut_occupation' => 'Importé via Excel'
                                        ], 'id_local');
                                    } else {
                                        // On crée un Lieu Public par défaut !
                                        $id_lieu = DB::table('lieux_publics')->insertGetId([
                                            'nom_lieu' => $lieu_recherche,
                                            'typologie_lieu' => 'Historique Excel'
                                        ], 'id_lieu');
                                    }
                                }
                            }
                        }
                    }
                }

                // 4. LOAD : Insertion dans ACTION
                DB::table('action')->updateOrInsert(
                    ['id_action' => $index],
                    [
                        'date_creation' => $date_ouverture,
                        'emetteur_nom' => 'Historique Excel',
                        'description' => substr($description_action, 0, 500),
                        'mode_reception' => 'Importation',
                        'priorite' => $priorite,
                        'statut_action' => $statut_action,
                        'id_user' => $id_user,
                        'id_cat' => $id_cat,
                        'id_lieu' => $id_lieu,
                        'id_batiment' => $id_batiment,
                        'id_local' => $id_local
                    ]
                );

                // 5. LOAD : Insertion dans INTERVENTION
                DB::table('intervention')->updateOrInsert(
                    ['id_int' => $index],
                    [
                        'code_budget' => $code_budget,
                        'date_ouverture' => $date_ouverture,
                        'date_cloture' => $date_cloture,
                        'type_intervention' => 'Maintenance corrective',
                        'statut_global' => empty($statut_global) ? 'En cours' : $statut_global,
                        'description' => $description_action,
                        'id_cat' => $id_cat,
                        'id_action' => $index,
                        'id_user_demandeur' => $id_user,
                        'id_batiment' => $id_batiment,
                        'id_local' => $id_local
                    ]
                );

                // 6. LOAD : Lien Intervention <-> Lieu (uniquement s'il y a un Lieu Public identifié)
                if ($id_lieu) {
                    DB::table('intervention_espace')->updateOrInsert([
                        'id_int' => $index,
                        'id_lieu' => $id_lieu
                    ]);
                }

                // 7. LOAD : Suivis d'action (Prochaine étape)
                foreach ($suivis as $etape) {
                    if (!empty($etape)) {
                        DB::table('suivi_action')->insert([
                            'date_action_suivi' => $date_cloture ?? now(),
                            'cout_associe' => $cout,
                            'statut_apres_action' => 'Planifié',
                            'description_etape' => $etape,
                            'id_int' => $index,
                            'id_user' => $id_user ?? 1
                        ]);
                    }
                }

                $compteur++;
            }

            // Réalignement des séquences PostgreSQL
            DB::statement("SELECT setval('action_id_action_seq', (SELECT MAX(id_action) FROM action))");
            DB::statement("SELECT setval('intervention_id_int_seq', (SELECT MAX(id_int) FROM intervention))");
            DB::statement("SELECT setval('local__id_local_seq', (SELECT MAX(id_local) FROM local_))");
            DB::statement("SELECT setval('lieux_publics_id_lieu_seq', (SELECT MAX(id_lieu) FROM lieux_publics))");

            DB::commit();
            fclose($file);

            $this->info("✅ Succès : $compteur lignes importées !");
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($file);
            $this->error("Erreur d'importation : " . $e->getMessage());
            return 1;
        }
    }
}