@extends('layouts.app')
@section('title','Produits')
@section('content')
<div class="flex items-center justify-between mb-4">
  <h2 class="text-xl font-semibold">Produits</h2>
  @if(in_array(auth()->user()->role ?? 'user', ['admin','dev']))
    <a class="bg-avss78-accent text-white px-3 py-1 rounded" href="{{ route('products.create') }}">Nouveau</a>
  @endif
  </div>
@if(session('status'))<div class="bg-green-100 text-green-800 px-3 py-2 rounded mb-4">{{ session('status') }}</div>@endif
<div class="overflow-x-auto bg-white rounded shadow">
  <table class="min-w-full text-sm">
  <thead class="bg-gray-100"><tr><th class="px-3 py-2 text-left">#</th><th class="px-3 py-2 text-left">Nom</th><th class="px-3 py-2 text-left">Numéro de lot</th><th class="px-3 py-2 text-left">Actions</th></tr></thead>
    <tbody>
      @foreach($products as $p)
      <tr class="border-t">
        <td class="px-3 py-2">{{ $p->id }}</td>
        <td class="px-3 py-2">{{ $p->name }}</td>
        <td class="px-3 py-2">{{ $p->sku }}</td>
        <td class="px-3 py-2 space-x-2">
          @if(in_array(auth()->user()->role ?? 'user', ['admin','dev']))
            <a class="text-blue-600" href="{{ route('products.edit',$p->id) }}">Modifier</a>
            <form action="{{ route('products.destroy',$p->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?')">
              @csrf @method('DELETE')
              <button class="text-red-600">Supprimer</button>
            </form>
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
