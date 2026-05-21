@extends('layouts.app')

@section('header_title', 'Ouvrage - ' . $ouvrage->nom_ouvrage)

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl border border-slate-200 shadow-sm space-y-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $ouvrage->nom_ouvrage }}</h1>
                <p class="text-slate-500">Rattaché à la voie : <span class="font-semibold">{{ $ouvrage->nom_voie }}</span>
                </p>
            </div>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-bold text-xs uppercase">
                {{ $ouvrage->type_ouvrage }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-8 py-6 border-y border-slate-100">
            <div>
                <p class="text-slate-500 text-sm">Franchissement</p>
                <p class="font-bold text-slate-900">{{ $ouvrage->franchissement ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-sm">Domaine</p>
                <p class="font-bold text-slate-900">{{ $ouvrage->domaine ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-sm">Loi Didier / Classement</p>
                <div class="flex gap-2 mt-1">
                    <span
                        class="{{ $ouvrage->sous_loi_didier ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }} px-2 py-1 rounded text-[10px] font-bold">Loi
                        Didier</span>
                    <span
                        class="{{ $ouvrage->est_programme_national ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }} px-2 py-1 rounded text-[10px] font-bold">Programme
                        National</span>
                </div>
            </div>
        </div>

        <div>
            <h3 class="font-bold text-slate-800 mb-2">Commentaires</h3>
            <p class="text-slate-600 bg-slate-50 p-4 rounded-lg">{{ $ouvrage->commentaire ?? 'Aucun commentaire.' }}</p>
        </div>
    </div>
@endsection