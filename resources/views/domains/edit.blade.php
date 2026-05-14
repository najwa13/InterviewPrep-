@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Modifier le domaine</h1>

    <form action="{{ route('domains.update', $domain) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Nom du domaine
            </label>
            <input type="text" name="name" id="name"
                   value="{{ old('name', $domain->name) }}"
                   class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="color" class="block text-sm font-medium text-gray-700 mb-1">
                Couleur
            </label>
            <div class="flex items-center gap-3">
                <input type="color" name="color" id="color"
                       value="{{ old('color', $domain->color) }}"
                       class="h-10 w-20 rounded cursor-pointer">
                <span class="text-gray-600 text-sm">{{ old('color', $domain->color) }}</span>
            </div>
            @error('color')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Enregistrer
            </button>
            <a href="{{ route('domains.index') }}"
               class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection