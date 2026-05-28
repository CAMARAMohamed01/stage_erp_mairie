@extends('layouts.app')
@section('header_title', 'Acte : ' . $decision->numero_decision)

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 pb-12">

        <!-- 🌟 EN-TÊTE AVEC LES NOUVEAUX BOUTONS D'ACTION -->
        <div
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xl font-bold">
                    📜</div>
                <div>
                    <h1 class="text-xl font-mono font-bold text-slate-900">{{ $decision->numero_decision }}</h1>
                    <p class="text-xs text-slate-500 font-medium">Type d'acte :
                        {{ $decision->type_decision ?? 'Non précisé' }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <a href="{{ route('decisions-admin.index') }}"
                    class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 transition w-full md:w-auto text-center">
                    ← Registre
                </a>

                <!-- Bouton Modifier -->
                @can('check-permission', ['Administration', 'ecriture'])
                    <a href="{{ route('decisions-admin.edit', $decision->id_decision) }}"
                        class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-bold transition shadow-sm w-full md:w-auto text-center">
                        ✏️ Modifier
                    </a>
                @endcan

                <!-- Bouton Supprimer -->
                @can('check-permission', ['Administration', 'suppression'])
                    <form action="{{ route('decisions-admin.destroy', $decision->id_decision) }}" method="POST"
                        onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer définitivement cet acte officiel ?');"
                        class="w-full md:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg text-sm font-bold transition">
                            🗑️ Supprimer
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <!-- LE RESTE DE LA PAGE NE CHANGE PAS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase">Intitulé officiel</span>
                        <h2 class="text-base font-bold text-slate-900 mt-0.5">{{ $decision->intitule_decision }}</h2>
                    </div>

                    <div class="border-t pt-3">
                        <span class="text-xs font-bold text-slate-400 uppercase">Objet détaillé / Corps de l'acte</span>
                        <p class="text-sm text-slate-700 mt-1 leading-relaxed bg-slate-50 p-4 rounded-lg border font-serif">
                            {{ $decision->objet_decision ?? 'Aucun texte saisi.' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3
                        class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2 flex justify-between">
                        <span>💰 Impacts Budgétaires</span>
                        <span
                            class="bg-slate-100 px-2 py-0.5 rounded-full text-xs text-slate-600">{{ $decision->operationsComptables->count() }}</span>
                    </h3>

                    <div class="space-y-2">
                        @forelse($decision->operationsComptables as $op)
                            <div
                                class="p-3 bg-slate-50 border rounded-lg flex justify-between items-center text-xs group relative">
                                <div>
                                    <p class="font-mono font-bold text-blue-600">N° {{ $op->numero_operation }}</p>
                                    <p class="text-slate-800 font-medium mt-0.5">{{ $op->libelle_operation }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Nature :
                                        {{ $op->nature_operation ?? 'Non spécifiée' }}
                                    </p>
                                </div>

                                @can('check-permission', ['Administration', 'suppression'])
                                    <form
                                        action="{{ route('decisions-admin.operations.destroy', [$decision->id_decision, $op->id_operation]) }}"
                                        method="POST" onsubmit="return confirm('Dissocier cette écriture comptable de l\'acte ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-red-500 hover:text-red-700 font-bold text-sm pl-2">×</button>
                                    </form>
                                @endcan
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic text-center py-2">Aucune ligne comptable associée pour le
                                moment.</p>
                        @endforelse
                    </div>

                    @can('check-permission', ['Administration', 'ecriture'])
                        <form action="{{ route('decisions-admin.operations.store', $decision->id_decision) }}" method="POST"
                            class="pt-3 border-t border-dashed space-y-2">
                            @csrf
                            <label class="block text-[11px] font-bold text-slate-500 uppercase">🔗 Imputer une opération
                                comptable</label>
                            <select name="id_operation" required
                                class="w-full text-xs border border-slate-300 rounded p-2 bg-white">
                                <option value="">-- Sélectionner l'écriture --</option>
                                @foreach($toutesLesOperations as $o)
                                    <option value="{{ $o->id_operation }}">[{{ $o->numero_operation }}] {{ $o->libelle_operation }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-2 rounded transition shadow-sm">
                                Valider l'affectation de l'acte
                            </button>
                        </form>
                    @endcan
                </div>

                <div class="bg-white p-4 rounded-xl border border-slate-200 text-xs space-y-2 shadow-sm">
                    <p class="text-slate-500 font-semibold flex justify-between"><span>📅 Prise d'acte :</span> <strong
                            class="text-slate-800">{{ $decision->date_decision->format('d/m/Y') }}</strong></p>
                    <p class="text-slate-500 font-semibold flex justify-between"><span>✍️ Rédacteur :</span> <strong
                            class="text-slate-800">👤 {{ $decision->redacteur->nom_user ?? 'Non précisé' }}</strong></p>
                    <div class="border-t pt-2 mt-2 flex justify-between items-center">
                        <span class="text-slate-500 font-semibold">Télétransmission Préfecture :</span>
                        <span
                            class="px-2 py-0.5 rounded font-bold text-[10px] {{ $decision->teletransmission_prefecture ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $decision->teletransmission_prefecture ? '✓ Envoyé' : '⏳ En attente' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection