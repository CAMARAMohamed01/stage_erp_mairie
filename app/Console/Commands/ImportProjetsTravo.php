<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportProjetsTravo extends Command
{
    protected $signature = 'import:projets-travo {filepath : Le chemin absolu vers le fichier CSV}';
    protected $description = 'Importe les projets, cumule les budgets et lie les périmètres multiples (Batiments, Lieux, Voies, Quartiers).';

    public function handle()
    {
        $filepath = $this->argument('filepath');

        if (!file_exists($filepath)) {
            $this->error("Le fichier est introuvable : {$filepath}");
            return Command::FAILURE;
        }

        $this->info("🚀 Analyse du fichier des Projets : {$filepath}...");

        $file = fopen($filepath, 'r');
        $rawHeaders = fgetcsv($file, 0, ';');

        // Nettoyage extrême des en-têtes
        $headers = array_map(function ($col) {
            $col = preg_replace('/[\xef\xbb\xbf]/', '', $col);
            $col = mb_convert_encoding(trim($col), 'UTF-8', 'Windows-1252');
            return Str::slug($col, '_');
        }, $rawHeaders);

        DB::beginTransaction();

        try {
            // Dictionnaires pour éviter les requêtes répétitives
            $utilisateurs = DB::table('utilisateur')->pluck('id_user', 'initiales')->toArray();

            $batimentsDico = [];
            foreach (DB::table('batiment')->get(['id_batiment', 'nom_bat']) as $b) {
                $batimentsDico[Str::slug($b->nom_bat)] = $b->id_batiment;
            }

            $lieuxDico = [];
            foreach (DB::table('lieux_publics')->get(['id_lieu', 'nom_lieu']) as $l) {
                $lieuxDico[Str::slug($l->nom_lieu)] = $l->id_lieu;
            }

            $voiesDico = [];
            foreach (DB::table('voie')->get(['id_voie', 'nom_voie']) as $v) {
                if ($v->nom_voie)
                    $voiesDico[Str::slug($v->nom_voie)] = $v->id_voie;
            }

            // --- ÉTAPE 1 : Aggrégation des données en mémoire ---
            // Puisque plusieurs lignes concernent le même projet, on les groupe d'abord !
            $projetsAcreer = [];

            while (($data = fgetcsv($file, 0, ';')) !== false) {
                if (count($headers) !== count($data))
                    continue;

                $data = array_map(function ($cell) {
                    return mb_convert_encoding(trim($cell), 'UTF-8', 'Windows-1252');
                }, $data);

                $row = array_combine($headers, $data);

                $nomProjet = trim($row['nom_projet'] ?? '');
                if (empty($nomProjet))
                    continue; // On ignore les lignes sans nom de projet

                $cleProjet = Str::slug($nomProjet);

                // Initialisation du projet dans le tableau s'il n'existe pas encore
                if (!isset($projetsAcreer[$cleProjet])) {
                    $projetsAcreer[$cleProjet] = [
                        'nom_projet' => $nomProjet,
                        'budget_total' => 0,
                        'initiales' => 'PYP', // Par défaut
                        'batiments' => [],
                        'lieux' => [],
                        'quartiers' => [],
                        'voies' => [],
                    ];
                }

                // 1. Cumul du budget
                $projetsAcreer[$cleProjet]['budget_total'] += $this->parseCost($row['montant_ttc'] ?? '');

                // 2. Récupération des initiales (on prend le premier qui passe)
                if (!empty($row['initiales'])) {
                    $projetsAcreer[$cleProjet]['initiales'] = $row['initiales'];
                }

                // 3. Collecte des entités liées (Périmètre)
                $nomBatiment = '';
                foreach ($row as $key => $value) {
                    if (str_contains($key, 'batiment')) {
                        $nomBatiment = trim($value);
                        break;
                    }
                }

                if (!empty($nomBatiment)) {
                    // --- LE DICTIONNAIRE D'ALIAS ---
                    // Si le nom du CSV ne correspond pas au nom de la BDD, on le traduit ici.
                    // Clé = Nom dans Excel (en minuscules), Valeur = Nom dans la Base de données
                    $aliases = [
                        'espace sportif et associatif' => 'EAS',
                        'ecole maurice anjot' => 'École Maurice Anjot',
                        // Ajoute d'autres traductions ici au besoin !
                    ];

                    $nomRecherche = $nomBatiment;
                    $nomMinuscule = strtolower($nomBatiment);

                    // Si on trouve une traduction dans notre dictionnaire, on l'utilise
                    if (array_key_exists($nomMinuscule, $aliases)) {
                        $nomRecherche = $aliases[$nomMinuscule];
                    }

                    // Recherche en base avec le nom traduit
                    $idBatiment = DB::table('batiment')
                        ->where('nom_bat', 'ILIKE', '%' . $nomRecherche . '%')
                        ->value('id_batiment');

                    if ($idBatiment) {
                        $projetsAcreer[$cleProjet]['batiments'][] = $idBatiment;
                    } else {
                        $this->warn("⚠️ Bâtiment introuvable en base de données : " . $nomBatiment);
                    }
                }

                $nomLieuPublic = trim($row['lieu_public'] ?? '');
                if ($nomLieuPublic && isset($lieuxDico[Str::slug($nomLieuPublic)])) {
                    $projetsAcreer[$cleProjet]['lieux'][] = $lieuxDico[Str::slug($nomLieuPublic)];
                }

                $nomQuartier = trim($row['quartier'] ?? '');
                if ($nomQuartier) {
                    // On vérifie en live s'il existe, sinon on le crée
                    $idQuartier = DB::table('lieu_dit')->where('nom_lieu_dit', 'ILIKE', $nomQuartier)->value('id_lieu_dit');
                    if (!$idQuartier) {
                        $idQuartier = DB::table('lieu_dit')->insertGetId(['nom_lieu_dit' => $nomQuartier], 'id_lieu_dit');
                    }
                    $projetsAcreer[$cleProjet]['quartiers'][] = $idQuartier;
                }

                $nomVoie = trim($row['nom_voie'] ?? '');
                if ($nomVoie) {
                    $idVoie = $voiesDico[Str::slug($nomVoie)] ?? null;
                    if (!$idVoie) {
                        $idVoie = DB::table('voie')->insertGetId(['nom_voie' => $nomVoie], 'id_voie');
                        $voiesDico[Str::slug($nomVoie)] = $idVoie; // Mise à jour du dico
                    }
                    $projetsAcreer[$cleProjet]['voies'][] = $idVoie;
                }
            }
            fclose($file);

            // --- ÉTAPE 2 : Insertion en Base de Données ---
            $compteurProjets = 0;

            foreach ($projetsAcreer as $p) {
                // Gestion Utilisateur
                $idUser = $utilisateurs[$p['initiales']] ?? null;
                if (!$idUser) {
                    $idUser = DB::table('utilisateur')->insertGetId([
                        'initiales' => substr($p['initiales'], 0, 5),
                        'nom_user' => 'Agent (' . $p['initiales'] . ')',
                        'prenom_user' => 'Import',
                        'role_appli' => 'Agent technique',
                    ], 'id_user');
                    $utilisateurs[$p['initiales']] = $idUser; // Mise à jour du dico
                }

                // Insertion du Projet
                $idProjet = DB::table('projet')->insertGetId([
                    'nom_projet' => $p['nom_projet'],
                    'budget_global_alloue' => $p['budget_total'] > 0 ? $p['budget_total'] : null,
                    'annee_mandat' => '2026', // Annee par defaut si absente
                    'type_projet' => 'Import Historique',
                    'id_user' => $idUser
                ], 'id_projet');

                // Liaisons (array_unique pour éviter de lier deux fois la même entité)
                foreach (array_unique($p['batiments']) as $id) {
                    DB::table('projet_batiment')->insertOrIgnore(['id_projet' => $idProjet, 'id_batiment' => $id]);
                }
                foreach (array_unique($p['lieux']) as $id) {
                    DB::table('projet_lieu')->insertOrIgnore(['id_projet' => $idProjet, 'id_lieu' => $id]);
                }
                foreach (array_unique($p['quartiers']) as $id) {
                    DB::table('projet_quartier')->insertOrIgnore(['id_projet' => $idProjet, 'id_lieu_dit' => $id]);
                }
                foreach (array_unique($p['voies']) as $id) {
                    DB::table('projet_voie')->insertOrIgnore(['id_projet' => $idProjet, 'id_voie' => $id]);
                }

                $compteurProjets++;
            }

            DB::commit();

            $this->info("✅ Succès : {$compteurProjets} projets uniques ont été créés et rattachés à leurs périmètres.");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($file) && is_resource($file))
                fclose($file);
            Log::error("Erreur ETL Projets : " . $e->getMessage());
            $this->error("🛑 L'import a échoué. Annulation complète. Erreur : " . $e->getMessage() . " à la ligne " . $e->getLine());
            return Command::FAILURE;
        }
    }

    private function parseCost($costStr)
    {
        $costStr = trim($costStr ?? '');
        if (empty($costStr) || $costStr === '-')
            return 0.00;

        $costStr = str_replace([',', ' ', '€'], ['.', '', ''], $costStr);
        $cleanCost = preg_replace('/[^0-9.\-]/', '', $costStr);

        return is_numeric($cleanCost) ? (float) $cleanCost : 0.00;
    }
}