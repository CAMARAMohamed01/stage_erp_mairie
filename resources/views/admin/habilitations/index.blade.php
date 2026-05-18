@extends('layouts.app')

@section('header_title', 'Gestion des Habilitations du Personnel')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Matrice des Droits par Agent</h1>
        <p class="text-sm text-slate-500 mt-1">Attribuez directement des droits CRUD et de validation aux agents de la
            commune.</p>
    </div>

    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-6 flex items-center justify-between">
        <form action="{{ route('admin.habilitations.index') }}" method="GET" id="userForm"
            class="flex items-center gap-4">
            <label for="id_user" class="text-sm font-semibold text-slate-700">Sélectionner un agent communal :</label>
            <select name="id_user" id="id_user" onchange="document.getElementById('userForm').submit();"
                class="border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 text-sm font-medium text-slate-800">
                @foreach($utilisateurs as $user)
                <option value="{{ $user->id_user }}" {{ $id_user_selectionne == $user->id_user ? 'selected' : '' }}>
                    👤 {{ $user->prenom_user }} {{ $user->nom_user }} ({{ $user->role_appli }})
                </option>
                @endforeach
            </select>
        </form>

        <span class="text-xs bg-green-50 text-green-700 border border-green-200 px-3 py-1.5 rounded-lg font-medium">
            Modèle Direct : Relation Individuelle Active
        </span>
    </div>

    @if($id_user_selectionne)
    <form action="{{ route('admin.habilitations.update') }}" method="POST"
        class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf
        <input type="hidden" name="id_user" value="{{ $id_user_selectionne }}">

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-1/3">Module Applicatif
                    </th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Lecture (R)
                    </th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Écriture (W)
                    </th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Suppression
                        (D)</th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Validation (V)
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($modules as $module)
                @php
                $hasLecture = $habilitations[$module->id_module]->droit_lecture ?? false;
                $hasEcriture = $habilitations[$module->id_module]->droit_ecriture ?? false;
                $hasSuppression = $habilitations[$module->id_module]->droit_suppression ?? false;
                $hasValidation = $habilitations[$module->id_module]->droit_validation ?? false;
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="p-4">
                        <p class="text-sm font-semibold text-slate-800">🖥️ {{ $module->nom_module }}</p>
                    </td>
                    <td class="p-4 text-center">
                        <input type="checkbox" name="droits[{{ $module->id_module }}][lecture]" value="1"
                            {{ $hasLecture ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                    </td>
                    <td class="p-4 text-center">
                        <input type="checkbox" name="droits[{{ $module->id_module }}][ecriture]" value="1"
                            {{ $hasEcriture ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                    </td>
                    <td class="p-4 text-center">
                        <input type="checkbox" name="droits[{{ $module->id_module }}][suppression]" value="1"
                            {{ $hasSuppression ? 'checked' : '' }} class="w-4 h-4 text-red-600 rounded">
                    </td>
                    <td class="p-4 text-center">
                        <input type="checkbox" name="droits[{{ $module->id_module }}][validation]" value="1"
                            {{ $hasValidation ? 'checked' : '' }} class="w-4 h-4 text-green-600 rounded">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
            <button type="submit"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                💾 Sauvegarder les permissions de l'agent
            </button>
        </div>
    </form>
    @endif
</div>
@endsection