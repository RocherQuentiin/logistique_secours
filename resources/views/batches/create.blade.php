@extends('layouts.app')
@section('title','Nouveau lot')
@section('content')
<div class="card p-5 max-w-2xl">
  <div class="form-toolbar">
    <h2 class="text-xl font-semibold">Nouveau lot</h2>
    <a href="{{ route('batches.index') }}" class="btn btn-ghost" title="Retour">⬅️</a>
  </div>
  <form method="POST" action="{{ route('batches.store') }}" class="space-y-4">@csrf
    <div>
      <label class="label">Produit</label>
      <select name="product_id" class="select" required>
        @foreach($products as $p)
          <option value="{{ $p->id }}">{{ $p->name }} (Numéro de lot: {{ $p->sku }})</option>
        @endforeach
      </select>
      <p class="form-help">Sélectionnez le produit associé à ce lot.</p>
    </div>
    <div>
      <label class="label">Nom du lot</label>
      <input name="name" class="input" required>
      <p class="form-help">Un identifiant clair pour distinguer ce lot.</p>
    </div>
    <div>
      <label class="label">Lieu</label>
      <select name="location_id" class="select" required>
        @foreach($locations as $l)
          <option value="{{ $l->id }}">{{ $l->name }}</option>
        @endforeach
      </select>
      <p class="form-help">Emplacement actuel du lot.</p>
    </div>
    <div>
      <label class="label">Quantité</label>
      <input type="number" name="quantity" min="0" class="input" required>
      <p class="form-help">Nombre d’unités dans ce lot.</p>
    </div>
    <div>
      <label class="label">Date de péremption</label>
      <input type="date" name="expiry_date" class="input">
      <p class="form-help">Optionnelle. Laissez vide si non applicable.</p>
    </div>
    <div class="divider"></div>
    <div class="flex items-center gap-2">
      <button class="btn btn-primary">Créer</button>
      <a href="{{ route('batches.index') }}" class="btn btn-ghost">Annuler</a>
    </div>
  </form>
</div>
@endsection
