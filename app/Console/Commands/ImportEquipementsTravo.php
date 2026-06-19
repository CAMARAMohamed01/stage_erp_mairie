<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportEquipementsTravo extends Command
{
    protected $signature = 'import:equipements-travo {filepath : Le chemin absolu vers le fichier CSV}';
    protected $description = 'Importe le catalogue des équipements et gère la hiérarchie parent/enfant (sous-équipements).';

    public function handle()
    {
        $filepath = $this->argument('filepath');

        if (!file_exists($filepath)) {
            $this->error("Le fichier est introuvable : {$filepath}");
            return Command::FAILURE;
        }

        $this->info("🚀 Analyse du fichier des Équipements : {$filepath}...");

        $file = fopen($filepath, 'r');
        // On suppose un CSV séparé par des points-virgules
        $rawHeaders = fgetcsv($file, 0, ';');

        DB::beginTransaction();

        try {
            // 1. Gestion de la famille obligatoire
            // La table equipement exige un id_famille NOT NULL. On en crée une par défaut pour ce catalogue.
            $nomFamilleDefaut = 'Catalogue Général';
            $idFamille = DB::table('famille_equipement')->where('libelle_famille', 'ILIKE', $nomFamilleDefaut)->value('id_famille');

            if (!$idFamille) {
                $idFamille = DB::table('famille_equipement')->insertGetId([
                    'libelle_famille' => $nomFamilleDefaut
                ], 'id_famille');
                $this->info("💡 Famille d'équipement '{$nomFamilleDefaut}' créée.");
            }

            $equipementsParentsCrees = [];
            $compteurParents = 0;
            $compteurEnfants = 0;

            // 2. Lecture ligne par ligne
            while (($data = fgetcsv($file, 0, ';')) !== false) {
                // On s'assure d'avoir au moins la première colonne
                if (empty(trim($data[0] ?? ''))) {
                    continue;
                }

                // Nettoyage de l'encodage pour les accents français
                $nomParent = mb_convert_encoding(trim($data[0]), 'UTF-8', 'Windows-1252');
                $nomEnfant = isset($data[1]) ? mb_convert_encoding(trim($data[1]), 'UTF-8', 'Windows-1252') : null;

                $cleParent = Str::slug($nomParent);

                // --- GESTION DU PARENT ---
                // On vérifie si on a déjà créé ce parent (pour ne pas créer 5 fois "camion")
                if (!isset($equipementsParentsCrees[$cleParent])) {
                    // Vérification en base au cas où
                    $idParentDB = DB::table('equipement')->where('nom_equipement', 'ILIKE', $nomParent)->value('id_equipement');

                    if (!$idParentDB) {
                        $idParentDB = DB::table('equipement')->insertGetId([
                            'nom_equipement' => Str::limit($nomParent, 80),
                            'etat_fonctionnement' => 'Opérationnel',
                            'id_famille' => $idFamille,
                            'remarque' => '[Import Catalogue]'
                        ], 'id_equipement');
                        $compteurParents++;
                    }
                    // On le stocke dans notre dictionnaire temporaire
                    $equipementsParentsCrees[$cleParent] = $idParentDB;
                }

                $idParentActuel = $equipementsParentsCrees[$cleParent];

                // --- GESTION DE L'ENFANT (SOUS-ÉQUIPEMENT) ---
                if (!empty($nomEnfant)) {
                    // Vérification que cet enfant n'existe pas déjà pour ce parent précis
                    $existeEnfant = DB::table('equipement')
                        ->where('nom_equipement', 'ILIKE', $nomEnfant)
                        ->where('id_parent', $idParentActuel)
                        ->exists();

                    if (!$existeEnfant) {
                        DB::table('equipement')->insert([
                            'nom_equipement' => Str::limit($nomEnfant, 80),
                            'etat_fonctionnement' => 'Opérationnel',
                            'id_famille' => $idFamille,
                            'id_parent' => $idParentActuel,
                            'remarque' => '[Import Catalogue - Sous-équipement]'
                        ]);
                        $compteurEnfants++;
                    }
                }
            }

            fclose($file);
            DB::commit();

            $this->info("✅ Succès : {$compteurParents} équipements principaux et {$compteurEnfants} sous-équipements importés !");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($file) && is_resource($file))
                fclose($file);
            Log::error("Erreur ETL Equipements : " . $e->getMessage());
            $this->error("🛑 L'import a échoué. Erreur : " . $e->getMessage() . " à la ligne " . $e->getLine());
            return Command::FAILURE;
        }
    }
}