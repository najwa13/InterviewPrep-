@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Concepts archivés — {{ $domain->name }}</h1>
        <a href="{{ route('domains.concepts.index', $domain) }}"
           class="text-blue-600 hover:underline">← Retour aux concepts</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @forelse($concepts as $concept)
        <div class="bg-white rounded-lg shadow p-6 mb-4 flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">
                    Archivé le {{ $concept->deleted_at->format('d/m/Y à H:i') }}
                </p>
                <h3 class="text-lg font-semibold">{{ $concept->title }}</h3>
                <div class="flex gap-2 mt-2">
                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                        {{ $concept->difficulty_label }}
                    </span>
                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                        {{ $concept->status_label }}
                    </span>
                </div>
            </div>
            <form action="{{ route('domains.concepts.restore', [$domain, $concept]) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Restaurer
                </button>
            </form>
        </div>
    @empty
        <p class="text-gray-600 text-center py-8">Aucun concept archivé.</p>
    @endforelse
</div>
@endsection