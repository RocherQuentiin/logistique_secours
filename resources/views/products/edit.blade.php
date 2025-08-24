@extends('layouts.app')
@section('title','Modifier produit')
@section('content')
<div class="card p-5 max-w-xl">
  <div class="form-toolbar">
    <h2 class="text-xl font-semibold">Modifier produit</h2>
    <a href="{{ route('products.index') }}" class="btn btn-ghost" title="Retour">⬅️</a>
  </div>
  <form method="POST" action="{{ route('products.update',$product->id) }}" class="space-y-4">@csrf @method('PUT')
    <div>
      <label class="label">Nom</label>
      <input name="name" value="{{ old('name',$product->name) }}" class="input" required>
      <p class="form-help">Nom lisible pour identifier rapidement le produit.</p>
    </div>
    <div>
      <label class="label">Numéro de lot</label>
      <input name="sku" value="{{ old('sku',$product->sku) }}" class="input" required>
      <p class="form-help">Référence interne (ex: LOT-2025-001).</p>
    </div>
    <div class="divider"></div>
    <div class="flex items-center gap-2">
      <button class="btn btn-primary">Enregistrer</button>
      <a href="{{ route('products.index') }}" class="btn btn-ghost">Annuler</a>
    </div>
  </form>
</div>
@endsection
