<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportUtilisateurs extends Command
{
    // Nom de la commande à taper dans le terminal avec un argument "fichier"
    protected $signature = 'import:utilisateurs {fichier : Chemin vers le fichier CSV (ex: storage/app/imports/utilisateurs.csv)}';

    protected $description = 'ETL : Importe, nettoie et insère les utilisateurs depuis un fichier CSV';

    public function handle()
    {
        $fichier = $this->argument('fichier');

        if (!file_exists($fichier)) {
            $this->error("Le fichier $fichier est introuvable !");
            return 1;
        }

        $this->info("Début de l'importation des utilisateurs...");

        // EXTRACT : Ouverture du fichier en mode lecture
        $file = fopen($fichier, 'r');

        // On récupère la première ligne (les en-têtes) pour l'ignorer
        $header = fgetcsv($file, 1000, ';'); // Remplace ';' par ',' si ton CSV est séparé par des virgules

        $compteur = 0;

        // On parcourt le fichier ligne par ligne
        while (($data = fgetcsv($file, 1000, ';')) !== FALSE) {

            $prenom = trim($data[0]);
            $nom = trim($data[1]);
            $initiales = trim($data[2]);
            $prenomPropre = Str::title(strtolower($prenom));
            $nomPropre = Str::title(strtolower($nom));
            $initialesPropres = strtoupper($initiales); // Toujours en MAJUSCULES


            DB::table('utilisateur')->updateOrInsert(
                [
                    'nom_user' => $nomPropre,
                    'prenom_user' => $prenomPropre
                ],
                [
                    'initiales' => $initialesPropres,
                    'role_appli' => 'Agent', // Defaut

                    'emailpro' => strtolower($prenomPropre . '.' . $nomPropre . '@dingysaintclair.fr'),
                    'password' => bcrypt('password'),
                ]
            );

            $compteur++;
        }

        fclose($file);

        $this->info("✅ Succès : $compteur utilisateurs ont été importés et nettoyés !");
        return 0;
    }
}