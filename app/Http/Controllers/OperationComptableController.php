<?php

namespace App\Http\Controllers;

use App\Models\OperationComptable;
use Illuminate\Http\Request;

class OperationComptableController extends Controller
{
    public function index(Request $request)
    {
        $query = OperationComptable::query();
        if ($request->filled('search')) {
            $query->where('numero_operation', 'ilike', '%' . $request->search . '%')
                ->orWhere('libelle_operation', 'ilike', '%' . $request->search . '%');
        }
        $operations = $query->orderBy('numero_operation')->paginate(15);
        return view('finances.operations.index', compact('operations'));
    }

    public function create()
    {
        return view('finances.operations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_operation' => 'required|string|max:20|unique:operation_comptable,numero_operation',
            'libelle_operation' => 'nullable|string|max:150',
            'nature_operation' => 'nullable|string|max:80',
        ]);
        OperationComptable::create($validated);
        return redirect()->route('operations-comptables.index')->with('success', 'Écriture comptable référencée.');
    }

    public function show($id)
    {
        $operation = OperationComptable::findOrFail($id);
        return view('finances.operations.show', compact('operation'));
    }

    public function edit($id)
    {
        $operation = OperationComptable::findOrFail($id);
        return view('finances.operations.edit', compact('operation'));
    }

    public function update(Request $request, $id)
    {
        $operation = OperationComptable::findOrFail($id);
        $validated = $request->validate([
            'numero_operation' => 'required|string|max:20|unique:operation_comptable,numero_operation,' . $id . ',id_operation',
            'libelle_operation' => 'nullable|string|max:150',
            'nature_operation' => 'nullable|string|max:80',
        ]);
        $operation->update($validated);
        return redirect()->route('operations-comptables.index')->with('success', 'Écriture mise à jour.');
    }

    public function destroy($id)
    {
        OperationComptable::findOrFail($id)->delete();
        return redirect()->route('operations-comptables.index')->with('success', 'Écriture supprimée.');
    }
}