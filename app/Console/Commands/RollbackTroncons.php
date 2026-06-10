<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RollbackTroncons extends Command
{
    protected $signature = 'erp:rollback-troncons';
    protected $description = 'Supprime proprement les tronçons, zones et secteurs importés par le CSV';

    public function handle()
    {
        if (!$this->confirm('⚠️ Voulez-vous vraiment supprimer les tronçons, zones et secteurs issus de l\'importation cadastrale ?')) {
            $this->info('Opération annulée.');
            return Command::SUCCESS;
        }

        $this->info('Début du nettoyage ciblé...');

        DB::beginTransaction();

        try {
            // 1. Suppression des tronçons dont le numéro commence par les secteurs du fichier
            $prefixes = ['A-', 'B-', 'C-', 'D-', 'E-', 'CVR1'];
            $deletedTroncons = 0;

            foreach ($prefixes as $prefix) {
                $deletedTroncons += DB::table('troncon')
                    ->where('numero_troncon', 'like', $prefix . '%')
                    ->delete();
            }

            // 2. Suppression des zones rattachées aux secteurs du fichier
            $secteursCibles = ['A', 'B', 'C', 'D', 'E', 'C PARTIE 1', 'C PARTIE 2'];

            $secteursIds = DB::table('secteur')
                ->whereIn('nom_secteur', $secteursCibles)
                ->pluck('id_secteur');

            $deletedZones = DB::table('zone')
                ->whereIn('id_secteur', $secteursIds)
                ->delete();

            // 3. Suppression des secteurs eux-mêmes
            $deletedSecteurs = DB::table('secteur')
                ->whereIn('id_secteur', $secteursIds)
                ->delete();

            DB::commit();

            $this->info('Marche arrière effectuée avec succès !');
            $this->line("- Tronçons supprimés : <fg=red>$deletedTroncons</>");
            $this->line("- Zones nettoyées : <fg=red>$deletedZones</>");
            $this->line("- Secteurs nettoyés : <fg=red>$deletedSecteurs</>");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Erreur lors du nettoyage. Opération annulée.');
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}