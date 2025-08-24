@extends('layouts.app')
@section('title','Lieux')
@section('content')
<div class="flex items-center justify-between mb-4">
  <h2 class="text-xl font-semibold">Lieux</h2>
  @if(in_array(auth()->user()->role ?? 'user', ['admin','dev']))
    <a class="btn btn-primary" href="{{ route('locations.create') }}">Nouveau</a>
  @endif
</div>
@if(session('status'))<div class="bg-green-100 text-green-800 px-3 py-2 rounded mb-4">{{ session('status') }}</div>@endif
<div class="card p-4 overflow-x-auto">
  <table class="data-table text-sm" data-stack>
    <thead><tr><th>#</th><th>Nom</th><th class="col-actions">Actions</th></tr></thead>
    <tbody>
      @foreach($locations as $l)
      <tr>
        <td data-label="#">{{ $l->id }}</td>
        <td data-label="Nom">{{ $l->name }}</td>
        <td class="col-actions" data-label="Actions">
          @if(in_array(auth()->user()->role ?? 'user', ['admin','dev']))
            <span class="actions">
            <a class="icon-btn btn-ghost" href="{{ route('locations.edit',$l->id) }}" title="Modifier" aria-label="Modifier">✏️</a>
            <form action="{{ route('locations.destroy',$l->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?')">
              @csrf @method('DELETE')
              <button class="icon-btn btn-danger" title="Supprimer" aria-label="Supprimer">🗑️</button>
            </form>
            </span>
          @else
            <span class="text-gray-400">—</span>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
