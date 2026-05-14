@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('domains.concepts.index', $domain) }}"
           class="text-blue-600 hover:underline">← Retour aux concepts</a>
    </div>

    <h1 class="text-2xl font-bold mb-6">Nouveau concept</h1>

    <form action="{{ route('domains.concepts.store', $domain) }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}"
                   class="w-full border rounded px-3 py-2 @error('title') border-red-500 @enderror"
                   placeholder="Ex: Eloquent N+1 Problem">
            @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="explanation" class="block text-sm font-medium text-gray-700 mb-1">Explication</label>
            <textarea name="explanation" id="explanation" rows="6"
                      class="w-full border rounded px-3 py-2 @error('explanation') border-red-500 @enderror"
                      placeholder="Expliquez le concept en détail...">{{ old('explanation') }}</textarea>
            @error('explanation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-1">Difficulté</label>
            <select name="difficulty" id="difficulty"
                    class="w-full border rounded px-3 py-2 @error('difficulty') border-red-500 @enderror">
                <option value="">-- Sélectionner --</option>
                <option value="junior" {{ old('difficulty') === 'junior' ? 'selected' : '' }}>Junior</option>
                <option value="mid" {{ old('difficulty') === 'mid' ? 'selected' : '' }}>Mid</option>
                <option value="senior" {{ old('difficulty') === 'senior' ? 'selected' : '' }}>Senior</option>
            </select>
            @error('difficulty')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Créer
            </button>
            <a href="{{ route('domains.concepts.index', $domain) }}"
               class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection