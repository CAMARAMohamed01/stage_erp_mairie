@extends('layouts.app')

@section('header_title', 'Ajouter un nouveau bâtiment')

@section('content')
<div class="max-w-4xl mx-auto pb-12">

    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Nouvelle Fiche Patrimoine</h1>
            <p class="text-sm text-slate-500 mt-1">Saisie complète d'un bâtiment et création d'entités liées à la volée.
            </p>
        </div>
        <a href="{{ route('batiments.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">←
            Annuler</a>
    </div>

    <form action="{{ route('batiments.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📋 Informations Générales
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nom du Bâtiment
                        <span class="text-red-500">*</span></label>
                    <input type="text" name="nom_bat" required placeholder="Ex: Groupe Scolaire Jean Moulin"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900 focus:border-slate-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Surface totale
                        (m²)</label>
                    <input type="number" step="0.01" name="surface_totale_m2"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900 focus:border-slate-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Date de
                        construction</label>
                    <input type="date" name="date_construction"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900 focus:border-slate-900">
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📍 Localisation & Cadastre
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Adresse Physique
                            <span class="text-red-500">*</span></label>
                        <button type="button" onclick="openModal('modalAdresse')"
                            class="text-xs text-blue-600 font-semibold hover:underline">➕ Créer adresse</button>
                    </div>
                    <select name="id_adresse" id="select_adresse" required
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50">
                        <option value="">-- Sélectionner --</option>
                        @foreach($adresses as $adresse)
                        <option value="{{ $adresse->id_adresse }}">{{ $adresse->num_rue }} {{ $adresse->nom_voie }},
                            {{ $adresse->ville }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Parcelle
                            Cadastrale <span class="text-red-500">*</span></label>
                        <button type="button" onclick="openModal('modalParcelle')"
                            class="text-xs text-blue-600 font-semibold hover:underline">➕ Créer parcelle</button>
                    </div>
                    <select name="id_parcelle" id="select_parcelle" required
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50">
                        <option value="">-- Sélectionner --</option>
                        @foreach($parcelles as $parcelle)
                        <option value="{{ $parcelle->id_parcelle }}">Section {{ $parcelle->section_cadastrale }} - N°
                            {{ $parcelle->num_parcelle }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">💼 Propriété &
                Classification</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                <div class="md:col-span-1 relative">
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Propriétaire
                            <span class="text-red-500">*</span></label>
                        <button type="button" onclick="openModal('modalTiers')"
                            class="text-xs text-blue-600 font-semibold hover:underline">➕ Créer</button>
                    </div>

                    <input type="text" id="search_tiers" placeholder="🔍 Rechercher un tiers..."
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900 focus:border-slate-900 mb-1">

                    <select name="id_tiers" id="select_tiers" required
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50">
                        <option value="">-- Sélectionner --</option>
                        @foreach($tiers as $t)
                        <option value="{{ $t->id_tiers }}">
                            {{ $t->raison_sociale ?? ($t->nom_tiers . ' ' . $t->prenom_tiers) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Classification
                        ERP <span class="text-red-500">*</span></label>
                    <select name="id_type_erp" required class="w-full rounded-lg border-slate-300 text-sm bg-slate-50">
                        <option value="">-- Sélectionner --</option>
                        @foreach($types_erp as $erp)
                        <option value="{{ $erp->id_type_erp }}">Cat. {{ $erp->categorie_erp }} - Type
                            {{ $erp->type_erp }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Immobilisation
                        Comptable <span class="text-red-500">*</span></label>
                    <select name="id_immo" id="select_immo" required
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50">
                        <option value="">-- Sélectionner --</option>
                        @foreach($immos_disponibles as $immo)
                        <option value="{{ $immo->id_immo }}">{{ $immo->num_inventaire }}
                            ({{ $immo->libelle_comptable }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit"
                class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg shadow-md transition">
                💾 Valider l'enregistrement
            </button>
        </div>
    </form>
</div>

<div id="modalAdresse"
    class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-md w-full p-6 space-y-4">
        <h3 class="text-base font-bold text-slate-900">➕ Ajouter une adresse</h3>
        <form id="formAdresse" class="space-y-3">
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500">N° Rue</label>
                    <input type="number" name="num_rue" required class="w-full rounded-md border-slate-300 text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-slate-500">Nom de la Voie</label>
                    <input type="text" name="nom_voie" required class="w-full rounded-md border-slate-300 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500">Code Postal</label>
                    <input type="text" name="code_postal" required class="w-full rounded-md border-slate-300 text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-slate-500">Ville</label>
                    <input type="text" name="ville" required class="w-full rounded-md border-slate-300 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500">Lieu-dit Rattaché</label>
                <select name="id_lieu_dit" required class="w-full rounded-md border-slate-300 text-sm bg-slate-50">
                    @foreach($lieu_dits as $ld)
                    <option value="{{ $ld->id_lieu_dit }}">{{ $ld->nom_lieu_dit }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('modalAdresse')"
                    class="px-3 py-2 border rounded-md text-xs font-semibold text-slate-600">Fermer</button>
                <button type="button"
                    onclick="submitModal('formAdresse', '{{ route('api.adresse.store') }}', 'select_adresse')"
                    class="px-3 py-2 bg-blue-600 text-white rounded-md text-xs font-semibold">Créer</button>
            </div>
        </form>
    </div>
</div>

<div id="modalParcelle"
    class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-md w-full p-6 space-y-4">
        <h3 class="text-base font-bold text-slate-900">➕ Créer une parcelle cadastrale</h3>
        <form id="formParcelle" class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500">Section (ex: A)</label>
                    <input type="text" name="section_cadastrale" maxlength="1" required
                        class="w-full rounded-md border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500">N° Parcelle (ex: 0142)</label>
                    <input type="text" name="num_parcelle" maxlength="5" required
                        class="w-full rounded-md border-slate-300 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500">Type de Parcelle</label>
                <input type="text" name="type_parcelle" placeholder="Domaine Public, Privé..."
                    class="w-full rounded-md border-slate-300 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500">Lieu-dit</label>
                    <select name="id_lieu_dit" required class="w-full rounded-md border-slate-300 text-sm bg-slate-50">
                        @foreach($lieu_dits as $ld)
                        <option value="{{ $ld->id_lieu_dit }}">{{ $ld->nom_lieu_dit }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500">Immobilisation liée</label>
                    <select name="id_immo" required class="w-full rounded-md border-slate-300 text-sm bg-slate-50">
                        @foreach($immos_disponibles as $immo)
                        <option value="{{ $immo->id_immo }}">{{ $immo->num_inventaire }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('modalParcelle')"
                    class="px-3 py-2 border rounded-md text-xs font-semibold text-slate-600">Fermer</button>
                <button type="button"
                    onclick="submitModal('formParcelle', '{{ route('api.parcelle.store') }}', 'select_parcelle')"
                    class="px-3 py-2 bg-blue-600 text-white rounded-md text-xs font-semibold">Créer</button>
            </div>
        </form>
    </div>
</div>

<div id="modalTiers" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-md w-full p-6 space-y-4">
        <h3 class="text-base font-bold text-slate-900">➕ Créer un Tiers (Propriétaire)</h3>
        <form id="formTiers" class="space-y-3">
            <div>
                <label class="block text-xs font-medium text-slate-500">Typologie du Tiers</label>
                <select name="type_tiers" id="toggle_type_tiers" onchange="switchTiersFields(this.value)"
                    class="w-full rounded-md border-slate-300 text-sm bg-slate-50">
                    <option value="Personne Morale">Personne Morale (Entreprise / Collectivité)</option>
                    <option value="Personne Physique">Personne Physique (Particulier)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500">Email Contact</label>
                <input type="email" name="email_tiers" class="w-full rounded-md border-slate-300 text-sm">
            </div>

            <div id="fields_morale" class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500">Raison Sociale</label>
                    <input type="text" name="raison_sociale" id="req_raison" required
                        class="w-full rounded-md border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500">Numéro SIRET</label>
                    <input type="text" name="siret" maxlength="14" class="w-full rounded-md border-slate-300 text-sm">
                </div>
            </div>

            <div id="fields_physique" class="space-y-3 hidden">
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-500">Civilité</label>
                        <input type="text" name="civilite" placeholder="M. / Mme"
                            class="w-full rounded-md border-slate-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500">Nom</label>
                        <input type="text" name="nom_tiers" id="req_nom"
                            class="w-full rounded-md border-slate-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500">Prénom</label>
                        <input type="text" name="prenom_tiers" id="req_prenom"
                            class="w-full rounded-md border-slate-300 text-sm">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500">Adresse Principale (Optionnelle)</label>
                <select name="id_adresse" class="w-full rounded-md border-slate-300 text-sm bg-slate-50">
                    <option value="">Aucune</option>
                    @foreach($adresses as $adr)
                    <option value="{{ $adr->id_adresse }}">{{ $adr->num_rue }} {{ $adr->nom_voie }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('modalTiers')"
                    class="px-3 py-2 border rounded-md text-xs font-semibold text-slate-600">Fermer</button>
                <button type="button"
                    onclick="submitModal('formTiers', '{{ route('api.tiers.store') }}', 'select_tiers')"
                    class="px-3 py-2 bg-blue-600 text-white rounded-md text-xs font-semibold">Créer</button>
            </div>
        </form>
    </div>
</div>

<script>
// 1. GESTION DES FENÊTRES MODALES
function openModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

// Basculer l'affichage des champs Tiers Morale / Physique
function switchTiersFields(type) {
    const moraleBlock = document.getElementById('fields_morale');
    const physiqueBlock = document.getElementById('fields_physique');

    if (type === 'Personne Morale') {
        moraleBlock.classList.remove('hidden');
        physiqueBlock.classList.add('hidden');
        document.getElementById('req_raison').required = true;
        document.getElementById('req_nom').required = false;
        document.getElementById('req_prenom').required = false;
    } else {
        moraleBlock.classList.add('hidden');
        physiqueBlock.classList.remove('hidden');
        document.getElementById('req_raison').required = false;
        document.getElementById('req_nom').required = true;
        document.getElementById('req_prenom').required = true;
    }
}

// Envoi asynchrone (AJAX) des formulaires modaux
function submitModal(formId, targetUrl, selectDestId) {
    const formElement = document.getElementById(formId);
    const formData = new FormData(formElement);

    fetch(targetUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.id) {
                // Ajouter dynamiquement la nouvelle entité au sélecteur d'origine
                const selectElement = document.getElementById(selectDestId);
                const newOption = new Option(data.label, data.id, true, true);
                selectElement.add(newOption);

                // Si on a ajouté un tiers, mettre à jour la valeur de filtrage
                if (selectDestId === 'select_tiers') {
                    document.getElementById('search_tiers').value = data.label;
                }

                // Fermer la modale correspondante
                closeModal(formElement.closest('[id^="modal"]').id);
                formElement.reset();
            }
        })
        .catch(error => alert("Erreur d'insertion. Vérifiez la conformité des données foncières."));
}

// 2. MOTEUR DE RECHERCHE EN TEMPS RÉEL (FILTRAGE DES TIERS)
document.getElementById('search_tiers').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    const select = document.getElementById('select_tiers');
    const options = select.options;

    let matchingCount = 0;
    let firstMatchIndex = -1;

    for (let i = 0; i < options.length; i++) {
        const text = options[i].text.toLowerCase();
        const matches = text.includes(query);

        // On affiche ou masque visuellement via le script
        if (matches) {
            options[i].disabled = false;
            options[i].hidden = false;
            matchingCount++;
            if (firstMatchIndex === -1 && options[i].value !== "") firstMatchIndex = i;
        } else {
            if (options[i].value !== "") {
                options[i].disabled = true;
                options[i].hidden = true;
            }
        }
    }

    // Sélectionner automatiquement le premier résultat pertinent si la recherche est précise
    if (firstMatchIndex !== -1 && query.length > 2) {
        select.selectedIndex = firstMatchIndex;
    }
});
</script>
@endsection