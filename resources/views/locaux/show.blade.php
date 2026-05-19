@extends('layouts.app')

@section('header_title', 'Fiche Pièce - ' . $local->nom_local)

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div
            class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="text-3xl">🚪</span>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $local->nom_local }}</h1>
                    <span
                        class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                        Usage : {{ $local->libelle_usage ?? 'Non défini' }}
                    </span>
                    <span
                        class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $local->statut_occupation === 'Occupé' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-amber-50 text-amber-700 border-amber-200' }} border">
                        {{ $local->statut_occupation ?? 'Statut inconnu' }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1.5 flex items-center gap-1.5">
                    📍 Situé dans : <span class="text-slate-700 font-medium">
                        {{ $local->nom_bat ?? $local->nom_lieu ?? 'Aucun rattachement principal' }}
                        {{ $local->niveau ? '(Niveau : ' . $local->niveau . ')' : '' }}
                    </span>
                    | 📏 Surface : <span
                        class="text-slate-700 font-medium">{{ $local->surface_m2 ? $local->surface_m2 . ' m²' : 'Non mesurée' }}</span>
                </p>
            </div>
            <div class="flex gap-2">
                <button onclick="history.back()"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">
                    ← Retour
                </button>
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('locaux.edit', $local->id_local) }}"
                        class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">
                        Modifier la pièce
                    </a>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            ⚙️ Équipements présents ({{ $equipements->count() }})
                        </h3>
                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('equipements.create', ['id_local' => $local->id_local]) }}"
                                class="text-xs text-blue-600 font-semibold hover:underline">
                                ➕ Ajouter un équipement
                            </a>
                        @endcan
                    </div>
                    <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                        @forelse($equipements as $equip)
                            <div class="p-3.5 flex justify-between items-center hover:bg-slate-50 transition">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $equip->nom_equipement }}</p>
                                    <p class="text-xs text-slate-400">Réf : {{ $equip->reference_serie ?? 'N/A' }} | État :
                                        {{ $equip->etat_fonctionnement ?? 'Opérationnel' }}
                                    </p>
                                </div>
                                <a href="{{ route('equipements.show', $equip->id_equipement) }}"
                                    class="text-xs text-blue-600 font-semibold hover:underline">
                                    Voir →
                                </a>
                            </div>
                        @empty
                            <p class="p-4 text-xs text-slate-400 italic text-center">Aucun équipement inventorié dans cette
                                pièce.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            ⚡ Compteurs Réseaux ({{ $compteurs->count() }})
                        </h3>
                    </div>
                    <div class="p-4">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="text-slate-400 font-bold border-b border-slate-100 pb-2">
                                    <th class="pb-2">Point de comptage</th>
                                    <th class="pb-2">Réseau</th>
                                    <th class="pb-2 text-right">N° Compteur</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($compteurs as $compteur)
                                    <tr>
                                        <td class="py-2.5 font-semibold text-slate-800">{{ $compteur->point_comptage }}</td>
                                        <td class="py-2.5">
                                            <span
                                                class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded">{{ $compteur->type_reseau }}</span>
                                        </td>
                                        <td class="py-2.5 text-right text-slate-600 font-mono">
                                            {{ $compteur->numero_compteur ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-slate-400 italic">Aucun compteur associé à
                                            ce local.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-red-50/50 border-b border-red-100">
                        <h3 class="text-sm font-bold text-red-800 tracking-tight flex items-center gap-2">
                            🚨 Incidents en cours ({{ $signalements->count() }})
                        </h3>
                    </div>
                    <div class="p-4 space-y-3 max-h-52 overflow-y-auto">
                        @forelse($signalements as $sig)
                            <div class="p-2.5 bg-slate-50 border border-slate-150 rounded-lg text-xs">
                                <div class="flex justify-between font-semibold text-slate-800">
                                    <span class="truncate pr-2">⚠️ {{ $sig->statut_signalement }}</span>
                                    <span class="text-red-600 shrink-0">{{ $sig->priorite ?? 'Normale' }}</span>
                                </div>
                                <p class="text-slate-500 mt-1 line-clamp-2">{{ $sig->description }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic text-center py-2">Parfait ! Aucun incident déclaré ici.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight">📝 Notes & Assurances</h3>
                    </div>
                    <div class="p-4 text-sm text-slate-600 space-y-3">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase">Réf. Assurance</span>
                            {{ $local->ref_article_assurance ?? 'Non renseignée' }}
                            @if($local->prime_assurance_ttc)
                                <span class="text-slate-400">({{ $local->prime_assurance_ttc }} €)</span>
                            @endif
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase">Remarques</span>
                            {{ $local->remarque ?? 'Aucune observation.' }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection