@extends('layouts.app')

@section('header_title', 'Gestion des Ouvrages d\'Art')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-slate-900">Ouvrages d'Art</h1>
            <a href="{{ route('ouvrages.create') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700">+ Ajouter un ouvrage</a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 uppercase text-xs font-bold text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Nom</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Voie Portée</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($ouvrages as $o)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-semibold">{{ $o->nom_ouvrage }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $o->type_ouvrage }}</td>
                            <td class="px-6 py-4">{{ $o->nom_voie ?? 'Non affecté' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('ouvrages.show', $o->id_ouvrage) }}"
                                    class="text-blue-600 font-bold hover:underline">Détails</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection