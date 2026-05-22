@extends('layouts.app')

@section('header_title', 'Référentiel des Communes Partenaires')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                    <span class="text-3xl">🏛️</span> Communes Partenaires
                </h1>
                <p class="text-sm text-slate-500 mt-1">Gérez les collectivités avec lesquelles vous partagez la gestion des
                    ouvrages d'art.</p>
            </div>

            @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                <a href="{{ route('communes.create') }}"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
                    ➕ Ajouter une commune
                </a>
            @endcan
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 uppercase text-xs font-bold text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Nom de la commune</th>
                            <th class="px-6 py-4 text-center">Code Postal</th>
                            <th class="px-6 py-4 font-mono">SIRET</th>
                            <th class="px-6 py-4">Contact (Email)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($communes as $commune)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    {{ $commune->nom_commune }}
                                </td>
                                <td class="px-6 py-4 text-center text-slate-700 font-medium">
                                    {{ $commune->code_postal ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-slate-500 font-mono text-xs">
                                    {{ $commune->siret_mairie ?? 'Non renseigné' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($commune->email_contact)
                                        <a href="mailto:{{ $commune->email_contact }}"
                                            class="text-blue-600 font-semibold hover:underline">
                                            📧 {{ $commune->email_contact }}
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic">Aucun email</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <span class="text-4xl block mb-2">🤷‍♂️</span>
                                    <p class="text-slate-500 font-medium">Aucune commune partenaire n'est enregistrée pour le
                                        moment.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($communes->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $communes->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection