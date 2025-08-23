@extends('layouts.app')
@section('title','Modifier produit')
@section('content')
<h2 class="text-xl font-semibold mb-4">Modifier produit</h2>
<form method="POST" action="{{ route('products.update',$product->id) }}" class="space-y-4 max-w-md">@csrf @method('PUT')
  <div><label class="block text-sm">Nom</label><input name="name" value="{{ old('name',$product->name) }}" class="w-full border rounded px-2 py-1" required></div>
  <div><label class="block text-sm">Numéro de lot</label><input name="sku" value="{{ old('sku',$product->sku) }}" class="w-full border rounded px-2 py-1" required></div>
  <button class="bg-avss78-accent text-white px-3 py-1 rounded">Enregistrer</button>
</form>
@endsection
