@extends('layouts.app')
@section('title','Modifier lot')
@section('content')
<div class="card p-5 max-w-2xl">
  <div class="form-toolbar">
    <h2 class="text-xl font-semibold">Modifier lot</h2>
    <a href="{{ route('batches.index') }}" class="btn btn-ghost" title="Retour">⬅️</a>
  </div>
  <form method="POST" action="{{ route('batches.update',$batch->id) }}" class="space-y-4">@csrf @method('PUT')
    <div>
      <label class="label">Produit</label>
      <select name="product_id" class="select" required>
        @foreach($products as $p)
          <option value="{{ $p->id }}" @selected($batch->product_id==$p->id)>{{ $p->name }} (Numéro de lot: {{ $p->sku }})</option>
        @endforeach
      </select>
      <p class="form-help">Produit associé à ce lot.</p>
    </div>
    <div>
      <label class="label">Nom du lot</label>
      <input name="name" value="{{ old('name',$batch->name) }}" class="input" required>
      <p class="form-help">Un identifiant clair pour distinguer ce lot.</p>
    </div>
    <div>
      <label class="label">Lieu</label>
      <select name="location_id" class="select" required>
        @foreach($locations as $l)
          <option value="{{ $l->id }}" @selected($batch->location_id==$l->id)>{{ $l->name }}</option>
        @endforeach
      </select>
      <p class="form-help">Emplacement actuel du lot.</p>
    </div>
    <div>
      <label class="label">Quantité</label>
      <input type="number" name="quantity" min="0" value="{{ old('quantity',$batch->quantity) }}" class="input" required>
      <p class="form-help">Nombre d’unités dans ce lot.</p>
    </div>
    <div>
      <label class="label">Date de péremption</label>
      <input type="date" name="expiry_date" value="{{ optional($batch->expiry_date)->format('Y-m-d') }}" class="input">
      <p class="form-help">Optionnelle. Laissez vide si non applicable.</p>
    </div>
    <div class="divider"></div>
    <div class="flex items-center gap-2">
      <button class="btn btn-primary">Enregistrer</button>
      <a href="{{ route('batches.index') }}" class="btn btn-ghost">Annuler</a>
    </div>
  </form>
</div>
@endsection
