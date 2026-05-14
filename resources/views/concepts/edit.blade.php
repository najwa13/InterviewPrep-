@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('domains.concepts.index', [$domain, $concept]) }}"
           class="text-blue-600 hover:underline">← Retour au concept</a>
    </div>

    <h1 class="text-2xl font-bold mb-6">Modifier le concept</h1>

    <form action="{{ route('domains.concepts.update', [$domain, $concept]) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
            <input type="text" name="title" id="title"
                   value="{{ old('title', $concept->title) }}"
                   class="w-full border rounded px-3 py-2 @error('title') border-red-500 @enderror">
            @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="explanation" class="block text-sm font-medium text-gray-700 mb-1">Explication</label>
            <textarea name="explanation" id="explanation" rows="6"
                      class="w-full border rounded px-3 py-2 @error('explanation') border-red-500 @enderror">{{ old('explanation', $concept->explanation) }}</textarea>
            @error('explanation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-1">Difficulté</label>
            <select name="difficulty" id="difficulty"
                    class="w-full border rounded px-3 py-2 @error('difficulty') border-red-500 @enderror">
                <option value="junior" {{ $concept->difficulty === 'junior' ? 'selected' : '' }}>Junior</option>
                <option value="mid" {{ $concept->difficulty === 'mid' ? 'selected' : '' }}>Mid</option>
                <option value="senior" {{ $concept->difficulty === 'senior' ? 'selected' : '' }}>Senior</option>
            </select>
            @error('difficulty')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
            <select name="status" id="status"
                    class="w-full border rounded px-3 py-2 @error('status') border-red-500 @enderror">
                <option value="to_review" {{ $concept->status === 'to_review' ? 'selected' : '' }}>À revoir</option>
                <option value="in_progress" {{ $concept->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                <option value="mastered" {{ $concept->status === 'mastered' ? 'selected' : '' }}>Maîtrisé</option>
            </select>
            @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Enregistrer
            </button>
            <a href="{{ route('domains.concepts.index', $domain) }}"
               class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection