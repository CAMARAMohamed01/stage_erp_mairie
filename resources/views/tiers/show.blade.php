@extends('layouts.app')

@section('title', 'Dossier Citoyen : ' . ($citoyen->physique?->nom_tiers ?? 'Inconnu'))

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- ACTION ACTIONS -->
        <div class="flex justify-between items-center">
            <a href="{{ route('tiers.index') }}"
                class="text-slate-500 hover:text-slate-800 text-sm flex items-center font-medium transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Retour à l'annuaire
            </a>

            <div class="flex gap-2">
                <a href="{{ route('tiers.edit', $citoyen->id_tiers) }}"
                    class="px-4 py-2 bg-amber-100 text-amber-700 border border-amber-200 font-semibold text-sm rounded-lg hover:bg-amber-200 transition">
                    ✏️ Modifier
                </a>
                <form action="{{ route('tiers.destroy', $citoyen->id_tiers) }}" method="POST"
                    onsubmit="return confirm('Voulez-vous vraiment supprimer ce citoyen de la base de données ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-100 text-red-700 border border-red-200 font-semibold text-sm rounded-lg hover:bg-red-200 transition">
                        🗑️ Supprimer
                    </button>
                </form>
            </div>
        </div>

        <!-- EN-TÊTE ET PROFIL -->
        <div
            class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 flex flex-wrap gap-8 justify-between items-start">
            <div class="flex items-center gap-5">
                <div
                    class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-2xl font-bold shadow-inner">
                    {{ substr($citoyen->physique?->prenom_tiers ?? 'X', 0, 1) }}{{ substr($citoyen->physique?->nom_tiers ?? 'X', 0, 1) }}
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        @if($citoyen->physique?->civilite)
                            <span class="text-lg text-slate-500 font-semibold mt-1">{{ $citoyen->physique?->civilite }}</span>
                        @endif
                        {{ $citoyen->physique?->prenom_tiers ?? '' }}
                        {{ $citoyen->physique?->nom_tiers ?? 'Citoyen Inconnu' }}
                    </h1>
                    <p class="text-slate-500 font-medium mt-1">
                        Citoyen répertorié #{{ $citoyen->id_tiers }}
                    </p>
                </div>
            </div>

            <div class="bg-slate-50 p-5 rounded-lg border border-slate-100 min-w-[280px]">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 border-b border-slate-200 pb-1">
                    Identité</h3>
                <div class="space-y-2 mb-5">
                    @if($citoyen->physique?->date_naissance)
                        <div class="flex items-center text-slate-700 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            Né(e) le {{ \Carbon\Carbon::parse($citoyen->physique?->date_naissance)->format('d/m/Y') }}
                        </div>
                    @else
                        <p class="text-sm text-slate-400 italic">Date de naissance inconnue.</p>
                    @endif
                </div>

                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 border-b border-slate-200 pb-1">
                    Contact</h3>
                <div class="space-y-2">
                    @if($citoyen->tel_tiers)
                        <div class="flex items-center text-slate-700 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            {{ $citoyen->tel_tiers }}
                        </div>
                    @endif

                    @if($citoyen->email_tiers)
                        <div class="flex items-center text-slate-700 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ $citoyen->email_tiers }}
                        </div>
                    @endif

                    @if(!$citoyen->tel_tiers && !$citoyen->email_tiers)
                        <p class="text-sm text-slate-400 italic">Aucune coordonnée renseignée.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- 👨‍👩‍👧‍👦 FICHE ÉTAT CIVIL & FILIATIONS -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-200 bg-slate-50">
                <h2 class="text-lg font-bold text-slate-800 flex items-center">
                    <span>👨‍👩‍👧‍👦 Fiche d'État Civil & Filiations</span>
                </h2>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">

                <div class="space-y-4">
                    <!-- Conjoint & Statut Marital -->
                    <div class="p-4 bg-slate-50 border rounded-xl space-y-2">
                        <div class="flex justify-between items-center border-b pb-1">
                            <span class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">💍 Conjoint & Statut
                                Marital</span>
                        </div>
                        @if($union)
                            <div class="flex justify-between items-center pt-1">
                                <div>
                                    <a href="{{ route('tiers.show', $union->id_conjoint) }}"
                                        class="text-sm font-bold text-blue-600 hover:underline">
                                        {{ $union->nom_tiers }} {{ $union->prenom_tiers }}
                                    </a>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Type : {{ $union->type_union }}
                                        @if($union->date_union) le
                                        {{ \Carbon\Carbon::parse($union->date_union)->format('d/m/Y') }} @endif
                                    </p>
                                    @if($union->date_dissolution)
                                        <p class="text-[10px] text-red-600 font-bold mt-0.5">💔 Dissout le
                                            {{ \Carbon\Carbon::parse($union->date_dissolution)->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>
                                @if(!$union->date_dissolution)
                                    <form
                                        action="{{ route('tiers.union.dissoudre', [$citoyen->id_tiers, $union->id_tiers_id_partenaire1, $union->id_tiers_id_partenaire2]) }}"
                                        method="POST" onsubmit="return confirm('Enregistrer la dissolution de cette union ?');">
                                        @csrf
                                        <button type="submit"
                                            class="px-2 py-1 bg-red-50 text-red-700 border border-red-200 rounded font-bold hover:bg-red-100 transition">Dissoudre</button>
                                    </form>
                                @endif
                            </div>
                        @else
                            <p class="text-slate-400 italic py-1">Célibataire ou aucune union enregistrée en commune.</p>
                        @endif
                    </div>

                    <!-- Ascendance (Parents) -->
                    <div class="p-4 bg-slate-50 border rounded-xl space-y-2">
                        <span class="font-bold text-slate-700 block uppercase tracking-wider text-[10px] border-b pb-1">🔺
                            Ascendance (Parents)</span>
                        <div class="divide-y divide-slate-200">
                            @forelse($parents as $parent)
                                <div class="flex justify-between items-center py-2">
                                    <a href="{{ route('tiers.show', $parent->id_tiers) }}"
                                        class="font-bold text-slate-800 hover:text-blue-600">{{ $parent->nom_tiers }}
                                        {{ $parent->prenom_tiers }}</a>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="px-2 py-0.5 bg-slate-200 text-slate-600 rounded text-[10px] font-medium">{{ $parent->type_filiation }}</span>
                                        <form
                                            action="{{ route('tiers.filiation.destroy', [$citoyen->id_tiers, $parent->id_tiers]) }}"
                                            method="POST" onsubmit="return confirm('Retirer ce lien de parenté ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-slate-300 hover:text-red-500 font-bold">✕</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-slate-400 italic pt-1">Aucun parent renseigné au dossier.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Descendance (Enfants) -->
                    <div class="p-4 bg-slate-50 border rounded-xl space-y-2">
                        <span class="font-bold text-slate-700 block uppercase tracking-wider text-[10px] border-b pb-1">🔻
                            Descendance (Enfants)</span>
                        <div class="divide-y divide-slate-200">
                            @forelse($enfants as $enfant)
                                <div class="flex justify-between items-center py-2">
                                    <a href="{{ route('tiers.show', $enfant->id_tiers) }}"
                                        class="font-bold text-slate-800 hover:text-blue-600">{{ $enfant->nom_tiers }}
                                        {{ $enfant->prenom_tiers }}</a>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="px-2 py-0.5 bg-blue-50 text-blue-700 border rounded text-[10px] font-medium">{{ $enfant->type_filiation }}</span>
                                        <form
                                            action="{{ route('tiers.filiation.destroy', [$enfant->id_tiers, $citoyen->id_tiers]) }}"
                                            method="POST" onsubmit="return confirm('Retirer ce lien de filiation ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-slate-300 hover:text-red-500 font-bold">✕</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-slate-400 italic pt-1">Aucun enfant enregistré au livret de famille.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Formulaires d'imputation rapide -->
                <div class="space-y-4 border-t md:border-t-0 md:border-l pt-4 md:pt-0 md:pl-6 border-slate-200">
                    <form action="{{ route('tiers.union.store', $citoyen->id_tiers) }}" method="POST"
                        class="p-4 border rounded-xl space-y-2 bg-white shadow-sm">
                        @csrf
                        <span class="font-bold text-blue-600 block uppercase tracking-wider text-[10px]">✍️ Enregistrer un
                            Mariage / PACS</span>
                        <div class="space-y-2 pt-1">
                            <div>
                                <label class="block text-slate-500 mb-0.5">Sélectionner le Conjoint *</label>
                                <select name="id_partenaire2" required
                                    class="w-full border rounded-lg p-2 bg-white font-medium">
                                    <option value="">-- Choisir le citoyen --</option>
                                    @foreach($tousLesCitoyens as $tc)
                                        <option value="{{ $tc->id_tiers }}">{{ $tc->nom_tiers }} {{ $tc->prenom_tiers }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-500 mb-0.5">Type d'union *</label>
                                    <select name="type_union" required
                                        class="w-full border rounded-lg p-2 bg-white font-medium">
                                        <option value="Mariage Civil">Mariage Civil</option>
                                        <option value="PACS">PACS</option>
                                        <option value="Union Libre">Union Libre</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-500 mb-0.5">Date de célébration</label>
                                    <input type="date" name="date_union"
                                        class="w-full border rounded-lg p-1.5 bg-white font-mono">
                                </div>
                            </div>
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition mt-1">Célébrer
                                l'acte d'union</button>
                        </div>
                    </form>

                    <form action="{{ route('tiers.filiation.store', $citoyen->id_tiers) }}" method="POST"
                        class="p-4 border rounded-xl space-y-2 bg-white shadow-sm">
                        @csrf
                        <span class="font-bold text-emerald-600 block uppercase tracking-wider text-[10px]">✍️ Déclarer une
                            Filiation (Ascendant/Descendant)</span>
                        <div class="space-y-2 pt-1">
                            <div>
                                <label class="block text-slate-500 mb-0.5">Sélectionner le Parent ou l'Enfant *</label>
                                <select name="id_relatif" required
                                    class="w-full border rounded-lg p-2 bg-white font-medium">
                                    <option value="">-- Choisir le citoyen --</option>
                                    @foreach($tousLesCitoyens as $tc)
                                        <option value="{{ $tc->id_tiers }}">{{ $tc->nom_tiers }} {{ $tc->prenom_tiers }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-500 mb-0.5">Le citoyen choisi est mon : *</label>
                                    <select name="role_relatif" required
                                        class="w-full border rounded-lg p-2 bg-white font-bold text-slate-700">
                                        <option value="enfant">Enfant</option>
                                        <option value="parent">Parent</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-500 mb-0.5">Nature du lien</label>
                                    <select name="type_filiation" class="w-full border rounded-lg p-2 bg-white font-medium">
                                        <option value="Naturelle">Lien Naturel</option>
                                        <option value="Adoption Légitime">Adoption</option>
                                        <option value="Reconnaissance Anticipée">Reconnaissance</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-lg transition mt-1">Inscrire
                                le lien au registre</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- COMPTES BANCAIRES -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                    Comptes Bancaires
                </h2>
                <a href="{{ route('tiers.comptes.create', $citoyen->id_tiers) }}"
                    class="px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-lg hover:bg-blue-200 transition shadow-sm">
                    + Ajouter un compte
                </a>
            </div>

            <div class="p-6">
                @if(isset($citoyen->comptesBancaires) && $citoyen->comptesBancaires->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($citoyen->comptesBancaires as $compte)
                            <div
                                class="p-4 bg-slate-50 border border-slate-200 rounded-lg shadow-sm relative overflow-hidden group">
                                <div class="relative z-10">
                                    <div class="flex justify-between items-start mb-1">
                                        <p class="text-xs font-bold uppercase text-slate-500">Titulaire</p>
                                        <form action="{{ route('tiers.comptes.destroy', $compte->id_compte) }}" method="POST"
                                            class="inline-block" onsubmit="return confirm('Supprimer ce compte bancaire ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-500 transition"
                                                title="Supprimer le compte">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    <p class="text-sm font-semibold text-slate-800 mb-3">{{ $compte->titulaire_compte }}</p>

                                    <div class="bg-white p-2 rounded border border-slate-100 mb-2">
                                        <p class="text-[10px] text-slate-400 uppercase">IBAN</p>
                                        <p class="font-mono text-sm text-slate-900 tracking-wide">{{ $compte->iban }}</p>
                                    </div>

                                    <div class="flex justify-between items-end mt-2">
                                        <div>
                                            <p class="text-[10px] text-slate-400 uppercase">BIC</p>
                                            <p class="font-mono text-sm text-slate-700">{{ $compte->bic }}</p>
                                        </div>

                                        @if($compte->documents && $compte->documents->count() > 0)
                                            <div class="text-xs">
                                                @foreach($compte->documents as $doc)
                                                    <a href="{{ asset('storage/' . $doc->chemin_stockage) }}" target="_blank"
                                                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 font-semibold border border-blue-200 rounded hover:bg-blue-200 transition shadow-sm"
                                                        title="{{ $doc->nom_fichier }}">
                                                        📎 Voir le RIB
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center p-6 border-2 border-dashed border-slate-200 rounded-lg bg-slate-50">
                        <p class="text-sm text-slate-500 font-medium">Aucun compte bancaire enregistré pour ce citoyen.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- DOCUMENTS RATTACHÉS -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                        </path>
                    </svg>
                    Documents rattachés (KBIS, Contrats, etc.)
                </h2>
                <a href="{{ route('tiers.documents.create', $citoyen->id_tiers) }}"
                    class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg hover:bg-emerald-200 transition shadow-sm">
                    + Téléverser un document
                </a>
            </div>

            @if(isset($citoyen->documents) && $citoyen->documents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold">Fichier</th>
                                <th class="px-6 py-4 font-semibold">Type</th>
                                <th class="px-6 py-4 font-semibold">Date d'ajout</th>
                                <th class="px-6 py-4 font-semibold text-right">Taille</th>
                                <th class="px-6 py-4 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($citoyen->documents as $doc)
                                <tr class="hover:bg-slate-50 transition-colors text-sm">
                                    <td class="px-6 py-4 font-medium text-slate-800 flex items-center gap-2">
                                        📄 {{ $doc->nom_fichier }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <span
                                            class="px-2 py-1 bg-slate-100 text-slate-600 text-[10px] uppercase font-bold rounded">{{ $doc->type_doc }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">
                                        {{ \Carbon\Carbon::parse($doc->date_upload)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 text-right">
                                        {{ $doc->taille_ko }} Ko
                                    </td>
                                    <td class="px-6 py-4 text-right flex justify-end items-center gap-4">
                                        <a href="{{ asset('storage/' . $doc->chemin_stockage) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase tracking-wider">
                                            Voir ↓
                                        </a>
                                        <form action="{{ route('documents.global.destroy', $doc->id_document) }}" method="POST"
                                            onsubmit="return confirm('Voulez-vous vraiment supprimer ce document ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-500 hover:text-red-700 font-bold text-xs uppercase tracking-wider">
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-8 py-10 text-center border-t border-slate-100">
                    <p class="text-slate-500 font-medium">Aucun document n'est rattaché à ce dossier citoyen.</p>
                </div>
            @endif
        </div>

        <!-- HISTORIQUE DES REQUÊTES -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    Historique des requêtes
                </h2>
                <span class="text-sm text-slate-500 font-medium">{{ $citoyen->actions->count() }} demande(s) au total</span>
            </div>

            @if($citoyen->actions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold">Date</th>
                                <th class="px-6 py-4 font-semibold">N°</th>
                                <th class="px-6 py-4 font-semibold">Description de la demande</th>
                                <th class="px-6 py-4 font-semibold">Statut</th>
                                <th class="px-6 py-4 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($citoyen->actions as $action)
                                <tr class="hover:bg-slate-50 transition-colors text-sm">
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-600 font-medium">
                                        {{ \Carbon\Carbon::parse($action->date_creation)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-400">
                                        #{{ $action->id_action }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-slate-900 font-medium truncate max-w-xs"
                                            title="{{ $action->description }}">
                                            {{ Str::limit($action->description, 50) }}
                                        </div>
                                        <div class="text-xs text-slate-500 mt-1">Via {{ $action->mode_reception }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <x-badge type="statut" :value="$action->statut_action" />
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('actions.show', $action->id_action) }}"
                                            class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase tracking-wider">
                                            Consulter →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-8 py-12 text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2-2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                    </div>
                    <p class="text-slate-500 font-medium">Ce citoyen n'a aucune action dans son historique pour le moment.</p>
                </div>
            @endif
        </div>
    </div>
@endsection