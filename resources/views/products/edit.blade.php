@extends('layouts.app')
@section('title','Modifier produit')
@section('content')
<h2 class="text-xl font-semibold mb-4">Modifier produit</h2>
<form method="POST" action="{{ route('products.update',$product->id) }}" class="space-y-4 max-w-md">@csrf @method('PUT')
  <div>
    <label class="label">Nom</label>
    <input name="name" value="{{ old('name',$product->name) }}" class="input" required>
  </div>
  <div>
    <label class="label">Numéro de lot</label>
    <input name="sku" value="{{ old('sku',$product->sku) }}" class="input" required>
  </div>
  <button class="btn btn-primary">Enregistrer</button>
</form>
@endsection
