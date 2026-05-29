@extends('layouts.app')
@section('title', 'Fiche Immo #' . $immo->num_inventaire)

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">

    <div
        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <span class="text-3xl p-3 bg-blue-50 rounded-xl text-blue-600 shadow-sm select-none">📦</span>
            <div>
                <h1 class="text-xl font-bold text-slate-900">{{ $immo->libelle_comptable }}</h1>
                <p class="text-xs font-mono font-bold text-slate-500">N° INVENTAIRE : {{ $immo->num_inventaire }}</p>
            </div>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <a href="{{ route('immobilisations.index') }}"
                class="px-4 py-2 border border-slate-300 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-50 transition text-center flex-1 sm:flex-initial">←
                Liste</a>

            @if(auth()->user()->can('check-permission', ['Finances & Achats', 'ecriture']))
            <a href="{{ route('immobilisations.edit', $immo->id_immo) }}"
                class="px-4 py-2 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold rounded-lg hover:bg-amber-100 transition text-center flex-1 sm:flex-initial">✏️
                Modifier</a>
            @endif

            @if(auth()->user()->can('check-permission', ['Finances & Achats', 'suppression']))
            <form action="{{ route('immobilisations.destroy', $immo->id_immo) }}" method="POST"
                onsubmit="return confirm('⚠️ Supprimer définitivement cette immobilisation ? Les biens physiques rattachés seront libérés.');"
                class="flex-1 sm:flex-initial">
                @csrf @method('DELETE')
                <button type="submit"
                    class="w-full px-4 py-2 bg-red-50 border border-red-100 text-red-700 text-xs font-bold rounded-lg hover:bg-red-100 transition text-center">🗑️
                    Supprimer</button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="md:col-span-1 space-y-6">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b pb-2">Données Financières
                </h3>
                <div>
                    <span class="text-[10px] text-slate-400 block font-bold uppercase">Valeur d'acquisition brute</span>
                    <span
                        class="text-xl font-mono font-black text-slate-900">{{ number_format($immo->valeur_achat, 2, ',', ' ') }}
                        €</span>
                </div>
                <div class="text-xs space-y-2 pt-2 text-slate-600 font-semibold">
                    <div class="flex justify-between border-b pb-1.5"><span>Date d'achat :</span><span
                            class="font-mono text-slate-900">{{ $immo->date_acquisition ? $immo->date_acquisition->format('d/m/Y') : 'Non renseignée' }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-1.5"><span>Régime
                            :</span><span>{{ $immo->est_amortissable ? 'Amortissable' : 'Non Amortissable' }}</span>
                    </div>
                </div>

                @if($immo->date_sortie)
                <div class="p-3 bg-rose-50 border border-rose-100 rounded-lg text-xs text-rose-800 space-y-1">
                    <p class="font-bold">⚠️ ACTIF SORTI DE L'INVENTAIRE</p>
                    <p class="font-medium">Le : <span
                            class="font-mono font-bold">{{ $immo->date_sortie->format('d/m/Y') }}</span></p>
                    <p class="font-medium">Motif : {{ $immo->motif_sortie }}</p>
                </div>
                @endif
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b pb-2">Facture d'origine
                </h3>
                @if($immo->ligneAchat)
                <div class="text-xs space-y-2 font-medium">
                    <p class="font-semibold text-slate-900">« {{ $immo->ligneAchat->designation_ligne }} »</p>
                    <p class="text-[11px] text-slate-500 font-mono">Dossier N°#{{ $immo->ligneAchat->id_dossier_f }}</p>
                </div>
                @else
                <p class="text-xs text-slate-400 italic">Aucun maillage de facture directe.</p>
                @endif
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2">Patrimoine Communal
                    rattaché</h3>

                @if(
                $immo->batiments->isEmpty() && $immo->parcelles->isEmpty() && $immo->equipements->isEmpty() &&
                $immo->lieuxPublics->isEmpty()
                )
                <p class="text-xs text-slate-400 italic text-center py-6 bg-slate-50 rounded-xl border border-dashed">
                    Aucun bien physique n'est associé à cette fiche d'immobilisation pour le moment.</p>
                @else
                <div class="space-y-2 text-xs font-semibold text-slate-700">
                    @foreach($immo->batiments as $bat)
                    <div class="flex justify-between items-center p-3 bg-slate-50 border border-slate-100 rounded-lg">
                        <span class="flex items-center gap-2"><span
                                class="bg-indigo-100 text-indigo-700 text-[9px] font-bold px-1.5 py-0.5 rounded">BÂTIMENT</span>
                            🏢 {{ $bat->nom_bat }}</span>
                        <span class="text-slate-400 font-mono text-[11px]">{{ $bat->surface_totale_m2 ?? '—' }}
                            m²</span>
                    </div>
                    @endforeach

                    @foreach($immo->parcelles as $parc)
                    <div class="flex justify-between items-center p-3 bg-slate-50 border border-slate-100 rounded-lg">
                        <span class="flex items-center gap-2"><span
                                class="bg-emerald-100 text-emerald-700 text-[9px] font-bold px-1.5 py-0.5 rounded">CADASTRE</span>
                            🗺️ Section {{ $parc->section_cadastrale }} N°{{ $parc->num_parcelle }}</span>
                        <span class="text-slate-400 font-mono text-[11px]">{{ $parc->surface_cadastrale ?? '—' }}
                            m²</span>
                    </div>
                    @endforeach

                    @foreach($immo->equipements as $eq)
                    <div class="flex justify-between items-center p-3 bg-slate-50 border border-slate-100 rounded-lg">
                        <span class="flex items-center gap-2"><span
                                class="bg-amber-100 text-amber-700 text-[9px] font-bold px-1.5 py-0.5 rounded">MATÉRIEL</span>
                            ⚙️ {{ $eq->nom_equipement }}</span>
                        <span class="text-slate-400 font-mono text-[11px]">Série:
                            {{ $eq->reference_serie ?? '—' }}</span>
                    </div>
                    @endforeach

                    @foreach($immo->lieuxPublics as $lp)
                    <div class="flex justify-between items-center p-3 bg-slate-50 border border-slate-100 rounded-lg">
                        <span class="flex items-center gap-2"><span
                                class="bg-sky-100 text-sky-700 text-[9px] font-bold px-1.5 py-0.5 rounded">ESPACE
                                PUBLIC</span> 🌳 {{ $lp->nom_lieu }}</span>
                        <span
                            class="text-slate-400 text-[11px] font-sans italic font-normal text-slate-500">{{ $lp->typologie_lieu }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            @can('check-permission', ['Finances & Achats', 'ecriture'])
            <div class="bg-slate-900 text-slate-100 p-5 rounded-xl border border-slate-800 shadow-lg space-y-4">
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-blue-400">🔗 Mailler un actif physique de
                        la mairie</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Associez un composant de l'inventaire physique encore
                        libre à cette fiche d'immobilisation.</p>
                </div>

                @if($errors->has('liaison_error'))
                <div class="p-3 bg-red-950/50 border border-red-800 text-red-200 text-xs rounded-lg font-medium">
                    {{ $errors->first('liaison_error') }}
                </div>
                @endif

                <form action="{{ route('immobilisations.rattacher', $immo->id_immo) }}" method="POST"
                    class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end text-xs font-bold">
                    @csrf
                    <div class="sm:col-span-4">
                        <label class="block mb-1 text-slate-400">1. Nature du bien</label>
                        <select name="type_bien" id="type_bien" onchange="filterAssetList()" required
                            class="w-full border border-slate-700 rounded-lg p-2 bg-slate-800 text-white font-medium focus:outline-none">
                            <option value="">-- Choisir la catégorie --</option>
                            <option value="batiment">🏢 Bâtiment Communal</option>
                            <option value="parcelle">🗺️ Parcelle / Terrain</option>
                            <option value="equipement">⚙️ Matériel / Équipement</option>
                            <option value="lieu">🌳 Espace Public</option>
                        </select>
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block mb-1 text-slate-400">2. Actif à immobiliser</label>

                        <select id="select-empty"
                            class="w-full border border-slate-700 rounded-lg p-2 bg-slate-800 text-slate-500 font-medium"
                            disabled>
                            <option value="">Veuillez choisir une nature de bien...</option>
                        </select>

                        <select name="id_bien" id="select-batiment"
                            class="asset-select hidden w-full border border-slate-700 rounded-lg p-2 bg-slate-800 text-white font-medium">
                            <option value="">-- Sélectionner le bâtiment --</option>
                            @foreach($batimentsDisponibles as $b) <option value="{{ $b->id_batiment }}">
                                {{ $b->nom_bat }}</option> @endforeach
                        </select>

                        <select name="id_bien" id="select-parcelle"
                            class="asset-select hidden w-full border border-slate-700 rounded-lg p-2 bg-slate-800 text-white font-medium">
                            <option value="">-- Sélectionner la parcelle --</option>
                            @foreach($parcellesDisponibles as $p) <option value="{{ $p->id_parcelle }}">Section
                                {{ $p->section_cadastrale }} N°{{ $p->num_parcelle }}</option> @endforeach
                        </select>

                        <select name="id_bien" id="select-equipement"
                            class="asset-select hidden w-full border border-slate-700 rounded-lg p-2 bg-slate-800 text-white font-medium">
                            <option value="">-- Sélectionner le matériel --</option>
                            @foreach($equipementsDisponibles as $e) <option value="{{ $e->id_equipement }}">
                                {{ $e->nom_equipement }}</option> @endforeach
                        </select>

                        <select name="id_bien" id="select-lieu"
                            class="asset-select hidden w-full border border-slate-700 rounded-lg p-2 bg-slate-800 text-white font-medium">
                            <option value="">-- Sélectionner l'espace --</option>
                            @foreach($lieuxDisponibles as $l) <option value="{{ $l->id_lieu }}">{{ $l->nom_lieu }}
                            </option> @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <button type="submit"
                            class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs transition shadow-md">Lier
                            l'actif</button>
                    </div>
                </form>
            </div>
            @endcan
        </div>
    </div>
</div>

<script>
function filterAssetList() {
    const type = document.getElementById('type_bien').value;

    // On cache tous les selects d'actifs et on désactive leur name pour éviter les conflits POST
    document.querySelectorAll('.asset-select').forEach(select => {
        select.classList.add('hidden');
        select.disabled = true;
    });
    document.getElementById('select-empty').classList.add('hidden');

    if (!type) {
        document.getElementById('select-empty').classList.remove('hidden');
        return;
    }

    // On affiche et active le select correspondant à la nature choisie
    const activeSelect = document.getElementById('select-' + type);
    if (activeSelect) {
        activeSelect.classList.remove('hidden');
        activeSelect.disabled = false;
    }
}
</script>
@endsection