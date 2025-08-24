@extends('layouts.app')
@section('title','Produits')
@section('content')
<div class="flex items-center justify-between gap-3 mb-4">
  <h2 class="text-xl font-semibold">Produits</h2>
  <div class="flex items-center gap-2 ml-auto">
    <input data-table-search data-target="#productsTable" type="search" placeholder="Rechercher…" class="input w-48" />
    @if(in_array(auth()->user()->role ?? 'user', ['admin','dev']))
      <a class="btn btn-primary" href="{{ route('products.create') }}">Nouveau</a>
    @endif
  </div>
  </div>
@if(session('status'))<div class="bg-green-100 text-green-800 px-3 py-2 rounded mb-4">{{ session('status') }}</div>@endif
<div class="card p-4 overflow-x-auto">
  <table id="productsTable" class="data-table text-sm" data-stack>
  <thead><tr><th>#</th><th>Nom</th><th>Numéro de lot</th><th class="col-actions">Actions</th></tr></thead>
    <tbody>
      @foreach($products as $p)
      <tr>
        <td data-label="#">{{ $p->id }}</td>
        <td data-label="Nom">{{ $p->name }}</td>
        <td data-label="Numéro de lot">{{ $p->sku }}</td>
        <td class="col-actions" data-label="Actions">
          @if(in_array(auth()->user()->role ?? 'user', ['admin','dev']))
            <span class="actions">
            <a class="icon-btn btn-ghost" href="{{ route('products.edit',$p->id) }}" title="Modifier" aria-label="Modifier">
              ✏️
            </a>
            <form action="{{ route('products.destroy',$p->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?')">
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
