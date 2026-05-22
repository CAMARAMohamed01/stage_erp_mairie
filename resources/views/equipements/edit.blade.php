@extends('layouts.app')

@section('header_title', 'Modifier l\'équipement : ' . $equipement->nom_equipement)

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md border border-slate-100">

    <form action="{{ route('equipements.update', $equipement->id_equipement) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <div>
            <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 flex items-center">
                <span class="mr-2">📋</span> Informations Générales
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom / Désignation *</label>
                    <input type="text" name="nom_equipement"
                        value="{{ old('nom_equipement', $equipement->nom_equipement) }}" required
                        placeholder="Ex: Banc public, Chaudière..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Famille d'équipement *</label>
                    <select name="id_famille" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        <option value="">-- Sélectionner une famille --</option>
                        @foreach($familles as $famille)
                        <option value="{{ $famille->id_famille }}"
                            {{ $equipement->id_famille == $famille->id_famille ? 'selected' : '' }}>
                            {{ $famille->libelle_famille }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_famille')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Marque ou Référence</label>
                    <input type="text" name="marque" value="{{ old('marque', $equipement->marque) }}"
                        placeholder="Ex: Atlantic, Husqvarna..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Couleur</label>
                    <input type="text" name="couleur" value="{{ old('couleur', $equipement->couleur) }}"
                        placeholder="Ex: Gris, Vert sapin, RAL 6005..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">État de fonctionnement</label>
                    <select name="etat_fonctionnement"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        <option value="Opérationnel"
                            {{ $equipement->etat_fonctionnement == 'Opérationnel' ? 'selected' : '' }}>Opérationnel
                        </option>
                        <option value="En maintenance"
                            {{ $equipement->etat_fonctionnement == 'En maintenance' ? 'selected' : '' }}>En maintenance
                        </option>
                        <option value="En panne" {{ $equipement->etat_fonctionnement == 'En panne' ? 'selected' : '' }}>
                            En panne</option>
                        <option value="À réformer"
                            {{ $equipement->etat_fonctionnement == 'À réformer' ? 'selected' : '' }}>À réformer</option>
                    </select>
                </div>
                <div>
                    <label for="id_contrats" class="block text-sm font-medium text-slate-700 mb-1">
                        Contrats rattachés (Maintenance, Assurance...)
                    </label>
                    <select name="id_contrats[]" id="id_contrats" multiple size="4"
                        class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-white focus:ring-blue-500 text-sm">
                        @if(isset($contrats))
                        @foreach($contrats as $c)
                        @php
                        // Vérifie si l'équipement possède déjà ce contrat (pour le mode édition)
                        $isSelected = isset($equipement) && $equipement->contratsAdministratifs->contains('id_contrat',
                        $c->id_contrat);
                        // Ou s'il vient d'être sélectionné avant une erreur de validation
                        $isOldSelected = in_array($c->id_contrat, old('id_contrats', []));
                        @endphp
                        <option value="{{ $c->id_contrat }}" {{ ($isSelected || $isOldSelected) ? 'selected' : '' }}>
                            {{ $c->numero_contrat ?? 'Nouveau' }} - {{ $c->type_contrat }}
                        </option>
                        @endforeach
                        @endif
                    </select>
                    <span class="text-[10px] text-slate-500 mt-1 block">Maintenez CTRL (ou CMD sur Mac) pour
                        sélectionner plusieurs contrats.</span>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 flex items-center">
                <span class="mr-2">💳</span> Achat & Garantie
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° de série</label>
                    <input type="text" name="reference_serie"
                        value="{{ old('reference_serie', $equipement->reference_serie) }}"
                        placeholder="Ex: SN-123456789"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'achat</label>
                    <input type="date" name="date_achat" value="{{ old('date_achat', $equipement->date_achat) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Garantie (en mois)</label>
                    <input type="number" name="duree_garantie_mois"
                        value="{{ old('duree_garantie_mois', $equipement->duree_garantie_mois) }}" placeholder="Ex: 24"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 flex items-center">
                <span class="mr-2">📍</span> Localisation
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-4 rounded-lg border border-slate-200">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Situé dans le local :</label>
                    <select name="id_local"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm bg-white">
                        <option value="">-- Aucun local --</option>
                        @if(isset($locaux))
                        @foreach($locaux as $local)
                        <option value="{{ $local->id_local }}"
                            {{ $equipement->id_local == $local->id_local ? 'selected' : '' }}>
                            {{ $local->nom_local }}
                        </option>
                        @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Tronçon rattaché</label>
                    <select name="id_troncon" class="w-full border-slate-300 rounded-lg focus:ring-blue-500">
                        <option value="">-- Indépendant / Aucun --</option>
                        @foreach($troncons as $t)
                        <option value="{{ $t->id_troncon }}"
                            {{ old('id_troncon', request('id_troncon')) == $t->id_troncon ? 'selected' : '' }}>
                            {{ $t->numero_troncon }} {{ $t->nom_portion ? '('.$t->nom_portion.')' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">OU dans le lieu public :</label>
                    <select name="id_lieu"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm bg-white">
                        <option value="">-- Aucun lieu public --</option>
                        @if(isset($lieux))
                        @foreach($lieux as $lieu)
                        <option value="{{ $lieu->id_lieu }}"
                            {{ $equipement->id_lieu == $lieu->id_lieu ? 'selected' : '' }}>
                            {{ $lieu->nom_lieu }}
                        </option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 flex items-center">
                <span class="mr-2">🛡️</span> Contrôles réglementaires
            </h3>
            <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                @if(isset($controles) && $controles->count() > 0)
                <div class="space-y-4">
                    @foreach($controles as $controle)
                    @php
                    $relation = $equipement->controles->find($controle->id_controle);
                    @endphp

                    <div
                        class="flex flex-col md:flex-row md:items-center justify-between p-3 border-b border-slate-50 last:border-0 hover:bg-slate-50 rounded transition">
                        <label class="flex items-center space-x-3 cursor-pointer mb-2 md:mb-0">
                            <input type="checkbox" name="controles[]" value="{{ $controle->id_controle }}"
                                {{ $relation ? 'checked' : '' }}
                                class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                            <span class="text-gray-700 font-medium">
                                {{ $controle->designation }}
                                <span class="text-xs text-gray-400 font-normal ml-1">({{ $controle->frequence_mois }}
                                    mois)</span>
                            </span>
                        </label>

                        <div class="flex items-center">
                            <label class="text-xs text-gray-500 mr-2">Dernier contrôle :</label>
                            <input type="date" name="dates_controles[{{ $controle->id_controle }}]"
                                value="{{ $relation ? $relation->pivot->date_controle : '' }}"
                                class="text-sm border border-gray-300 rounded-md px-2 py-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500 italic text-center py-2">Aucun contrôle paramétré.</p>
                @endif
            </div>
        </div>

        <div class="flex justify-end pt-6 border-t border-gray-200">
            <a href="{{ route('equipements.show', $equipement->id_equipement) }}"
                class="px-6 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg mr-4 font-medium transition duration-150">
                Annuler
            </a>
            <button type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition duration-150">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection