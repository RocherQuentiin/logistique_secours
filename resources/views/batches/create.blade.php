@extends('layouts.app')
@section('title','Nouveau lot')
@section('content')
<h2 class="text-xl font-semibold mb-4">Nouveau lot</h2>
<form method="POST" action="{{ route('batches.store') }}" class="space-y-4 max-w-xl">@csrf
  <div><label class="label">Produit</label>
    <select name="product_id" class="select" required>
      @foreach($products as $p)
  <option value="{{ $p->id }}">{{ $p->name }} (Numéro de lot: {{ $p->sku }})</option>
      @endforeach
    </select>
  </div>
  <div><label class="label">Nom du lot</label><input name="name" class="input" required></div>
  <div><label class="label">Lieu</label>
    <select name="location_id" class="select" required>
      @foreach($locations as $l)
        <option value="{{ $l->id }}">{{ $l->name }}</option>
      @endforeach
    </select>
  </div>
  <div><label class="label">Quantité</label><input type="number" name="quantity" min="0" class="input" required></div>
  <div><label class="label">Date de péremption</label><input type="date" name="expiry_date" class="input"></div>
  <button class="btn btn-primary">Créer</button>
</form>
@endsection
