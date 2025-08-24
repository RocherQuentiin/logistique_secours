@extends('layouts.app')
@section('title','Lots')
@section('content')
<div class="flex items-center justify-between mb-4">
  <h2 class="text-xl font-semibold">Lots</h2>
  @if(in_array(auth()->user()->role ?? 'user', ['admin','dev']))
    <a class="btn btn-primary" href="{{ route('batches.create') }}">Nouveau</a>
  @endif
</div>
@if(session('status'))<div class="bg-green-100 text-green-800 px-3 py-2 rounded mb-4">{{ session('status') }}</div>@endif
<div class="card p-4 overflow-x-auto">
  <table class="data-table text-sm" data-stack>
    <thead><tr><th>#</th><th>Produit</th><th>Nom lot</th><th>Lieu</th><th>Qté</th><th>Péremption</th><th class="col-actions">Actions</th></tr></thead>
    <tbody>
      @foreach($batches as $b)
      <tr>
        <td data-label="#">{{ $b->id }}</td>
        <td data-label="Produit">{{ $b->product?->name }}</td>
        <td data-label="Nom lot">{{ $b->name }}</td>
        <td data-label="Lieu">{{ $b->location?->name }}</td>
        <td data-label="Qté">{{ $b->quantity }}</td>
        <td data-label="Péremption">{{ optional($b->expiry_date)->format('Y-m-d') }}</td>
        <td class="col-actions" data-label="Actions">
          @if(in_array(auth()->user()->role ?? 'user', ['admin','dev']))
            <span class="actions">
            <a class="icon-btn btn-ghost" href="{{ route('batches.edit',$b->id) }}" title="Modifier" aria-label="Modifier">✏️</a>
            <form action="{{ route('batches.destroy',$b->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?')">
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
