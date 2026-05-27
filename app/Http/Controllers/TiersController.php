<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tiers;
use App\Models\CompteBancaire;
use Illuminate\Support\Facades\DB;

class TiersController extends Controller
{
    // AFFICHER LA LISTE DE TOUS LES CITOYENS
    // AFFICHER LA LISTE DE TOUS LES CITOYENS
    public function index(Request $request)
    {
        // 1. On prépare la requête de base (sans faire de ->get() tout de suite !)
        $query = Tiers::with('physique')
            ->withCount('actions')
            ->where('type_tiers', 'Physique');

        // 2. Si on a une recherche, on ajoute les conditions à la requête
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                // Recherche sur les champs de la table principale
                $q->where('email_tiers', 'ilike', '%' . $search . '%')
                    ->orWhere('tel_tiers', 'ilike', '%' . $search . '%')
                    // Recherche sur la table liée (tiers_physique)
                    ->orWhereHas('physique', function ($subQuery) use ($search) {
                        $subQuery->where('nom_tiers', 'ilike', '%' . $search . '%')
                            ->orWhere('prenom_tiers', 'ilike', '%' . $search . '%');
                    });
            });
        }


        $citoyens = $query->get();

        // 4. On envoie les résultats filtrés à la vue
        return view('tiers.index', compact('citoyens'));
    }


    // AFFICHER LE PROFIL D'UN CITOYEN ET SON HISTORIQUE
    public function show($id)
    {
        $citoyen = Tiers::with([
            'physique',
            'comptesBancaires.documents',
            'documents',
            'actions' => function ($query) {
                $query->orderBy('date_creation', 'desc');
            }
        ])
            ->where('type_tiers', 'Physique') // ⚠️ AJOUT CRUCIAL ICI
            ->findOrFail($id);

        return view('tiers.show', compact('citoyen'));
    }
    // --- CRÉATION D'UN CITOYEN ---
    public function create()
    {
        return view('tiers.create');
    }
    // --- MODIFICATION D'UN CITOYEN ---
    public function edit($id)
    {
        $citoyen = Tiers::with('physique')->where('type_tiers', 'Physique')->findOrFail($id);
        return view('tiers.edit', compact('citoyen'));
    }

    // --- CRÉATION D'UN CITOYEN ---
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email_tiers' => 'nullable|email|max:100|unique:tiers,email_tiers',
            'tel_tiers' => 'nullable|string|max:12',
            'civilite' => 'nullable|string|max:50',
            'nom_tiers' => 'required|string|max:50',
            'prenom_tiers' => 'required|string|max:50',
            'date_naissance' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated) {
            $tiers = Tiers::create([
                'type_tiers' => 'Physique',
                'email_tiers' => $validated['email_tiers'],
                'tel_tiers' => $validated['tel_tiers'],
            ]);

            DB::table('tiers_physique')->insert([
                'id_tiers' => $tiers->id_tiers,
                'civilite' => $validated['civilite'],
                'nom_tiers' => $validated['nom_tiers'],
                'prenom_tiers' => $validated['prenom_tiers'],
                'date_naissance' => $validated['date_naissance'],
            ]);
        });

        return redirect()->route('tiers.index')->with('success', 'Citoyen ajouté avec succès.');
    }

    // --- MODIFICATION D'UN CITOYEN ---
    public function update(Request $request, $id)
    {
        $citoyen = Tiers::findOrFail($id);

        $validated = $request->validate([
            'email_tiers' => 'nullable|email|max:100|unique:tiers,email_tiers,' . $id . ',id_tiers',
            'tel_tiers' => 'nullable|string|max:12',
            'civilite' => 'nullable|string|max:50',
            'nom_tiers' => 'required|string|max:50',
            'prenom_tiers' => 'required|string|max:50',
            'date_naissance' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated, $citoyen) {
            $citoyen->update([
                'email_tiers' => $validated['email_tiers'],
                'tel_tiers' => $validated['tel_tiers'],
            ]);

            DB::table('tiers_physique')
                ->where('id_tiers', $citoyen->id_tiers)
                ->update([
                    'civilite' => $validated['civilite'],
                    'nom_tiers' => $validated['nom_tiers'],
                    'prenom_tiers' => $validated['prenom_tiers'],
                    'date_naissance' => $validated['date_naissance'],
                ]);
        });

        return redirect()->route('tiers.show', $citoyen->id_tiers)->with('success', 'Les informations du citoyen ont été mises à jour.');
    }

    // --- CRÉATION ---
    public function createEntreprise()
    {
        return view('tiers.create_entreprise');
    }
    // AFFICHER LE PROFIL D'UNE ENTREPRISE (Tiers Morale)
    public function showEntreprise($id)
    {
        // On charge l'entreprise avec ses infos morales et ses comptes bancaires (incluant les documents liés)

        $entreprise = Tiers::with(['morale', 'comptesBancaires.documents', 'documents',])->findOrFail($id);

        return view('tiers.show_entreprise', compact('entreprise'));
    }
    public function storeEntreprise(Request $request)
    {
        $validated = $request->validate([
            'email_tiers' => 'nullable|email|max:100|unique:tiers,email_tiers',
            'tel_tiers' => 'nullable|string|max:12',
            'raison_sociale' => 'required|string|max:150',
            'siret' => 'nullable|string|max:14',
            'numero_tva_intra' => 'nullable|string|max:30',
            'alias_tiers' => 'nullable|string|max:10',
            'nom_contact' => 'nullable|string|max:100',
            'num_compte_client' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($validated) {
            // 1. Insertion dans la table parente
            $tiers = Tiers::create([
                'type_tiers' => 'Morale',
                'email_tiers' => $validated['email_tiers'],
                'tel_tiers' => $validated['tel_tiers'],
            ]);

            // 2. Insertion dans la table enfant
            DB::table('tiers_morale')->insert([
                'id_tiers' => $tiers->id_tiers,
                'raison_sociale' => $validated['raison_sociale'],
                'siret' => $validated['siret'],
                'numero_tva_intra' => $validated['numero_tva_intra'],
                'alias_tiers' => $validated['alias_tiers'],
                'nom_contact' => $validated['nom_contact'],
                'num_compte_client' => $validated['num_compte_client'],
            ]);
        });

        return redirect()->route('tiers.entreprises')->with('success', 'Entreprise ajoutée avec succès.');
    }

    // --- MODIFICATION D'UNE ENTREPRISE ---
    public function editEntreprise($id)
    {
        $entreprise = Tiers::with('morale')->findOrFail($id);

        return view('tiers.edit_entreprise', compact('entreprise'));
    }

    public function updateEntreprise(Request $request, $id)
    {
        $entreprise = Tiers::findOrFail($id);

        $validated = $request->validate([
            // On ignore l'email actuel pour la règle "unique"
            'email_tiers' => 'nullable|email|max:100|unique:tiers,email_tiers,' . $id . ',id_tiers',
            'tel_tiers' => 'nullable|string|max:12',
            'raison_sociale' => 'required|string|max:150',
            'siret' => 'nullable|string|max:14',
            'numero_tva_intra' => 'nullable|string|max:30',
            'alias_tiers' => 'nullable|string|max:10',
            'nom_contact' => 'nullable|string|max:100',
            'num_compte_client' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($validated, $entreprise) {
            $entreprise->update([
                'email_tiers' => $validated['email_tiers'],
                'tel_tiers' => $validated['tel_tiers'],
            ]);

            DB::table('tiers_morale')
                ->where('id_tiers', $entreprise->id_tiers)
                ->update([
                    'raison_sociale' => $validated['raison_sociale'],
                    'siret' => $validated['siret'],
                    'numero_tva_intra' => $validated['numero_tva_intra'],
                    'alias_tiers' => $validated['alias_tiers'],
                    'nom_contact' => $validated['nom_contact'],
                    'num_compte_client' => $validated['num_compte_client'],
                ]);
        });

        return redirect()->route('tiers.entreprises')->with('success', 'Les informations ont été mises à jour.');
    }
    // AFFICHER L'ANNUAIRE DES ENTREPRISES (Tiers Morale)
    public function entreprises(Request $request)
    {
        // On charge la relation morale et les comptes bancaires (avec leurs documents)
        $query = Tiers::with(['morale', 'comptesBancaires.documents'])
            ->where('type_tiers', 'Morale')
            ->orWhere('type_tiers', 'Personne Morale');
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                // Recherche sur les contacts
                $q->where('email_tiers', 'ilike', '%' . $search . '%')
                    ->orWhere('tel_tiers', 'ilike', '%' . $search . '%')
                    // Recherche sur les infos de l'entreprise
                    ->orWhereHas('morale', function ($subQuery) use ($search) {
                        $subQuery->where('raison_sociale', 'ilike', '%' . $search . '%')
                            ->orWhere('siret', 'ilike', '%' . $search . '%')
                            ->orWhere('nom_contact', 'ilike', '%' . $search . '%');
                    });
            });
        }

        // On trie par ordre alphabétique de la raison sociale
        $entreprises = $query->get()->sortBy(function ($tiers) {
            return $tiers->morale->raison_sociale ?? 'ZZZ';
        });

        return view('tiers.entreprises', compact('entreprises'));
    }


    // --- SUPPRESSION GÉNÉRIQUE ---
    public function destroy($id)
    {
        $tiers = Tiers::findOrFail($id);
        $type = $tiers->type_tiers;

        DB::transaction(function () use ($tiers) {
            // Supprimer d'abord la dépendance dans la table enfant
            if ($tiers->type_tiers === 'Morale') {
                DB::table('tiers_morale')->where('id_tiers', $tiers->id_tiers)->delete();
            } else {
                DB::table('tiers_physique')->where('id_tiers', $tiers->id_tiers)->delete();
            }

            // Ensuite on supprime le parent
            $tiers->delete();
        });

        if ($type === 'Morale') {
            return redirect()->route('tiers.entreprises')->with('success', 'Entreprise supprimée définitivement.');
        }
        return redirect()->route('tiers.index')->with('success', 'Citoyen supprimé.');
    }

    // --- AFFICHER LE FORMULAIRE D'AJOUT D'UN COMPTE ---
    public function createCompte($id_tiers)
    {
        $tiers = Tiers::findOrFail($id_tiers);
        return view('tiers.comptes.create', compact('tiers'));
    }

    // --- ENREGISTRER LE COMPTE BANCAIRE ---
    public function storeCompte(Request $request, $id_tiers)
    {
        $tiers = Tiers::findOrFail($id_tiers);

        // 1. On valide les textes ET le document_rib
        $validated = $request->validate([
            'titulaire_compte' => 'required|string|max:100',
            'iban' => 'required|string|max:34',
            'bic' => 'required|string|max:11',
            'rib' => 'nullable|string|max:50',
            'document_rib' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120', // Validation du fichier (5Mo max)
        ]);

        // Nettoyage des espaces potentiels dans l'IBAN et le BIC
        $iban = str_replace(' ', '', strtoupper($validated['iban']));
        $bic = str_replace(' ', '', strtoupper($validated['bic']));

        // 2. Création du compte et récupération de l'instance (très important pour avoir l'id_compte)
        $compte = CompteBancaire::create([
            'titulaire_compte' => $validated['titulaire_compte'],
            'iban' => $iban,
            'bic' => $bic,
            'rib' => $validated['rib'] ?? null,
            'id_tiers' => $tiers->id_tiers,
            'date_ajout' => now()->format('Y-m-d'),
        ]);

        // 3. Traitement de l'upload du document s'il y en a un
        if ($request->hasFile('document_rib')) {
            $file = $request->file('document_rib');

            // Stockage physique dans storage/app/public/documents/comptes
            $path = $file->store('documents/comptes', 'public');

            // Création de l'enregistrement dans la table `document`
            \App\Models\Document::create([
                'nom_fichier' => $file->getClientOriginalName(),
                'type_doc' => 'RIB',
                'chemin_stockage' => $path,
                'taille_ko' => round($file->getSize() / 1024, 2),
                'date_upload' => now()->format('Y-m-d'),
                'id_compte' => $compte->id_compte, // C'est ici qu'on fait le lien avec le compte !
            ]);
        }

        // 4. On redirige vers la bonne vue
        if ($tiers->type_tiers !== 'Physique') {
            return redirect()->route('tiers.show_entreprise', $tiers->id_tiers)
                ->with('success', 'Compte bancaire et document ajoutés avec succès.');
        }

        return redirect()->route('tiers.show', $tiers->id_tiers)
            ->with('success', 'Compte bancaire et document ajoutés avec succès.');
    }
    // --- SUPPRIMER UN COMPTE BANCAIRE ---
    public function destroyCompte($id)
    {
        $compte = CompteBancaire::findOrFail($id);
        $tiers = Tiers::findOrFail($compte->id_tiers);

        // La contrainte 'set null' dans ta migration s'assurera que les documents 
        // liés à ce compte ne sont pas détruits, mais juste détachés.
        $compte->delete();

        if ($tiers->type_tiers === 'Morale' || $tiers->type_tiers === 'Personne Morale') {
            return redirect()->route('tiers.show_entreprise', $tiers->id_tiers)->with('success', 'Compte bancaire supprimé.');
        }
        return redirect()->route('tiers.show', $tiers->id_tiers)->with('success', 'Compte bancaire supprimé.');
    }
    // --- AFFICHER LE FORMULAIRE D'AJOUT D'UN DOCUMENT GÉNÉRAL ---
    public function createDocument($id_tiers)
    {
        $tiers = Tiers::findOrFail($id_tiers);
        return view('tiers.documents.create', compact('tiers'));
    }

    // --- ENREGISTRER LE DOCUMENT GÉNÉRAL ---
    public function storeDocument(Request $request, $id_tiers)
    {
        //dd($request->all());
        $tiers = Tiers::findOrFail($id_tiers);

        $request->validate([
            'type_doc' => 'required|string|max:50',
            'fichier' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120', // 5 Mo max
        ]);

        $file = $request->file('fichier');

        // On le range dans un dossier spécifique aux tiers
        $path = $file->store('documents/tiers', 'public');

        // Création dans la base de données
        \App\Models\Document::create([
            'nom_fichier' => $file->getClientOriginalName(),
            'type_doc' => $request->type_doc,
            'chemin_stockage' => $path,
            'taille_ko' => round($file->getSize() / 1024, 2),
            'date_upload' => now()->format('Y-m-d'),
            'id_tiers' => $tiers->id_tiers, // Le lien se fait ici !
        ]);

        // Redirection intelligente
        if ($tiers->type_tiers !== 'Physique') {
            return redirect()->route('tiers.show_entreprise', $tiers->id_tiers)->with('success', 'Document ajouté.');
        }
        return redirect()->route('tiers.show', $tiers->id_tiers)->with('success', 'Document ajouté.');
    }
}