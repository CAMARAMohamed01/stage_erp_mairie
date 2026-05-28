<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mt-6">
    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">
        🏢 Types d'ERP soumis à ce contrôle
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($typesErp as $erp)
            <label
                class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer transition">
                <div class="flex items-center h-5">
                    <input type="checkbox" name="types_erp[]" value="{{ $erp->id_type_erp }}"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                </div>
                <div class="text-sm">
                    <p class="font-bold text-slate-800">Catégorie {{ $erp->categorie_erp }} - Type {{ $erp->type_erp }}</p>
                    <p class="text-xs text-slate-500">{{ $erp->reglementation_applicable }}</p>
                </div>
            </label>
        @endforeach
    </div>
</div>