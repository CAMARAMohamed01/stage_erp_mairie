@extends('layouts.app') @section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Inventaire des Équipements</h1>
            <a href="{{ route('equipements.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                + Nouvel équipement
            </a>
        </div>

        <div class="bg-white shadow-md rounded my-6">
            <table class="text-left w-full border-collapse">
                <thead>
                    <tr>
                        <th
                            class="py-4 px-6 bg-gray-100 font-bold text-sm text-gray-700 uppercase border-b border-gray-200">
                            Nom / Désignation
                        </th>
                        <th
                            class="py-4 px-6 bg-gray-100 font-bold text-sm text-gray-700 uppercase border-b border-gray-200">
                            Famille
                        </th>
                        <th
                            class="py-4 px-6 bg-gray-100 font-bold text-sm text-gray-700 uppercase border-b border-gray-200">
                            Marque
                        </th>
                        <th
                            class="py-4 px-6 bg-gray-100 font-bold text-sm text-gray-700 uppercase border-b border-gray-200">
                            État
                        </th>
                        <th
                            class="py-4 px-6 bg-gray-100 font-bold text-sm text-gray-700 uppercase border-b border-gray-200">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipements as $equipement)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6 border-b border-gray-200">{{ $equipement->nom_equipement }}</td>

                            <td class="py-4 px-6 border-b border-gray-200">
                                <span
                                    class="bg-blue-50 text-blue-700 text-xs font-medium px-2 py-1 rounded border border-blue-200">
                                    {{ $equipement->famille->libelle_famille ?? 'Non classé' }}
                                </span>
                            </td>

                            <td class="py-4 px-6 border-b border-gray-200">{{ $equipement->marque ?? 'N/A' }}</td>
                            <td class="py-4 px-6 border-b border-gray-200">
                                @if($equipement->etat_fonctionnement == 'Opérationnel')
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">Opérationnel</span>
                                @elseif($equipement->etat_fonctionnement == 'En panne')
                                    <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">En panne</span>
                                @else
                                    <span
                                        class="bg-gray-100 text-gray-800 text-xs font-semibold px-2 py-1 rounded">{{ $equipement->etat_fonctionnement ?? 'Inconnu' }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 border-b border-gray-200">
                                <a href="{{ route('equipements.show', $equipement->id_equipement) }}"
                                    class="text-blue-500 hover:underline font-medium">Voir la fiche</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 px-6 border-b border-gray-200 text-center text-gray-500">
                                Aucun équipement enregistré dans la base de données.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection