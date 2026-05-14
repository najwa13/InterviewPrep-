@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('domains.concepts.index', $domain) }}"
           class="text-blue-600 hover:underline">← Retour aux concepts</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <h1 class="text-2xl font-bold">{{ $concept->title }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('domains.concepts.edit', [$domain, $concept]) }}"
                   class="text-blue-600 hover:text-blue-800">Modifier</a>
                <form action="{{ route('domains.concepts.destroy', [$domain, $concept]) }}" method="POST"
                      onsubmit="return confirm('Archiver ce concept ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800">Supprimer</button>
                </form>
            </div>
        </div>

        <div class="flex gap-2 mb-4">
            <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded">
                {{ $concept->difficulty_label }}
            </span>
            <span class="bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded">
                {{ $concept->status_label }}
            </span>
        </div>

        <div class="prose">
            <h3 class="text-lg font-semibold mb-2">Explication</h3>
            <p class="text-gray-700 whitespace-pre-wrap">{{ $concept->explanation }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Questions d'entretien</h2>

        <form action="{{ route('domains.concepts.generate', [$domain, $concept]) }}" method="POST" class="mb-6">
            @csrf
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                Générer des questions
            </button>
        </form>

        @forelse($concept->generatedQuestions->sortByDesc('created_at') as $generation)
            <div class="border rounded p-4 mb-4">
                <p class="text-gray-500 text-sm mb-2">
                    {{ $generation->created_at->format('d/m/Y à H:i') }}
                </p>
                <ol class="list-decimal list-inside space-y-1">
                    @foreach($generation->questions as $question)
                        <li class="text-gray-700">{{ $question }}</li>
                    @endforeach
                </ol>
                <form action="{{ route('domains.concepts.questions.destroy', [$domain, $concept, $generation]) }}"
                      method="POST" class="mt-2">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                        Supprimer cette génération
                    </button>
                </form>
            </div>
        @empty
            <p class="text-gray-500">Aucune génération pour l'instant.</p>
        @endforelse
    </div>
</div>
@endsection