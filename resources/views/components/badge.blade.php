@props(['type' => 'statut', 'value'])

@php
    // Configuration des styles selon le type de badge et sa valeur
    $styles = [
        'statut' => [
            'Nouveau' => 'bg-red-100 text-red-800 border-red-200',
            'En cours' => 'bg-blue-100 text-blue-800 border-blue-200',
            'Transmis' => 'bg-purple-100 text-purple-800 border-purple-200',
            'Terminé' => 'bg-green-100 text-green-800 border-green-200',
        ],
        'priorite' => [
            'Haute' => 'bg-orange-100 text-orange-800 border-orange-200',
            'Normale' => 'bg-slate-100 text-slate-800 border-slate-200',
            'Basse' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        ]
    ];

    // On récupère la classe correspondante ou une classe par défaut
    $class = $styles[$type][$value] ?? 'bg-gray-100 text-gray-800 border-gray-200';
@endphp

<span {{ $attributes->merge(['class' => "px-2.5 py-0.5 rounded-full text-xs font-medium border $class"]) }}>
    {{ $value }}
</span>