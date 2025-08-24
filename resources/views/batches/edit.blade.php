@extends('layouts.app')
@section('title','Modifier lot')
@section('content')
<h2 class="text-xl font-semibold mb-4">Modifier lot</h2>
<form method="POST" action="{{ route('batches.update',$batch->id) }}" class="space-y-4 max-w-xl">@csrf @method('PUT')
  <div><label class="label">Produit</label>
    <select name="product_id" class="select" required>
      @foreach($products as $p)
  <option value="{{ $p->id }}" @selected($batch->product_id==$p->id)>{{ $p->name }} (Numéro de lot: {{ $p->sku }})</option>
      @endforeach
    </select>
  </div>
  <div><label class="label">Nom du lot</label><input name="name" value="{{ old('name',$batch->name) }}" class="input" required></div>
  <div><label class="label">Lieu</label>
    <select name="location_id" class="select" required>
      @foreach($locations as $l)
        <option value="{{ $l->id }}" @selected($batch->location_id==$l->id)>{{ $l->name }}</option>
      @endforeach
    </select>
  </div>
  <div><label class="label">Quantité</label><input type="number" name="quantity" min="0" value="{{ old('quantity',$batch->quantity) }}" class="input" required></div>
  <div><label class="label">Date de péremption</label><input type="date" name="expiry_date" value="{{ optional($batch->expiry_date)->format('Y-m-d') }}" class="input"></div>
  <button class="btn btn-primary">Enregistrer</button>
</form>
@endsection
