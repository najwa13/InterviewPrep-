@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Concepts — {{ $domain->name }}</h1>
            <span class="inline-block w-4 h-4 rounded-full mt-1" style="background-color: {{ $domain->color }}"></span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('domains.concepts.archived', $domain) }}"
               class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Archivés
            </a>
            <a href="{{ route('domains.concepts.create', $domain) }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Nouveau concept
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex gap-2 mb-6">
        @foreach(['to_review' => 'À revoir', 'in_progress' => 'En cours', 'mastered' => 'Maîtrisé'] as $val => $label)
            <a href="{{ route('domains.concepts.index', [$domain, 'status' => $val]) }}"
               class="px-4 py-2 rounded {{ request('status') === $val ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' }}">
                {{ $label }}
            </a>
        @endforeach
        <a href="{{ route('domains.concepts.index', $domain) }}"
           class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 {{ request('status') ? '' : 'hidden' }}">
            Tous
        </a>
    </div>

    @forelse($concepts as $concept)
        <div class="bg-white rounded-lg shadow p-6 mb-4">
            <div class="flex justify-between items-start">
                <div>
                    <a href="{{ route('domains.concepts.show', [$domain, $concept]) }}"
                       class="text-lg font-semibold hover:text-blue-600">
                        {{ $concept->title }}
                    </a>
                    <div class="flex gap-2 mt-2">
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                            {{ $concept->difficulty_label }}
                        </span>
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                            {{ $concept->status_label }}
                        </span>
                    </div>
                </div>
                <form action="{{ route('domains.concepts.updateStatus', [$domain, $concept]) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">
                        → Suivant
                    </button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-gray-600 text-center py-8">
            Aucun concept.
            <a href="{{ route('domains.concepts.create', $domain) }}" class="text-blue-600 hover:underline">Créer le premier</a>
        </p>
    @endforelse
</div>
@endsection