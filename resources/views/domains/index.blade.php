@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Mes Domaines</h1>
        <a href="{{ route('domains.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Nouveau domaine
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @forelse($domains as $domain)
        <div class="bg-white rounded-lg shadow p-6 mb-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="inline-block w-4 h-4 rounded-full"
                      style="background-color: {{ $domain->color }}"></span>
                <div>
                    <a href="{{ route('domains.concepts.index', $domain) }}"
                       class="text-lg font-semibold hover:text-blue-600">
                        {{ $domain->name }}
                    </a>
                    <p class="text-gray-600 text-sm">
                        {{ $domain->concepts_count }} concept(s)
                        — {{ $domain->mastered_count }} maîtrisé(s)
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('domains.edit', $domain) }}"
                   class="text-blue-600 hover:text-blue-800">Modifier</a>
                <form action="{{ route('domains.destroy', $domain) }}" method="POST"
                      onsubmit="return confirm('Supprimer ce domaine et tous ses concepts ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-gray-600 text-center py-8">
            Aucun domaine. <a href="{{ route('domains.create') }}" class="text-blue-600 hover:underline">Créer le premier</a>
        </p>
    @endforelse
</div>
@endsection