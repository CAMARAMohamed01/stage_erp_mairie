<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SecteurImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Laravel Excel transforme "Code Secteur" en "code_secteur" automatiquement
            // et "Secteur" en "secteur"

            if (empty($row['code_secteur'])) {
                continue;
            }

            DB::table('secteur')->updateOrInsert(
                ['code_secteur' => $row['code_secteur']], // La clé unique pour vérifier l'existence
                [
                    'nom_secteur' => $row['secteur'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}