@extends('layouts.app')
@section('title','Modifier lot')
@section('content')
<h2 class="text-xl font-semibold mb-4">Modifier lot</h2>
<form method="POST" action="{{ route('batches.update',$batch->id) }}" class="space-y-4 max-w-xl">@csrf @method('PUT')
  <div><label class="block text-sm">Produit</label>
    <select name="product_id" class="w-full border rounded px-2 py-1" required>
      @foreach($products as $p)
  <option value="{{ $p->id }}" @selected($batch->product_id==$p->id)>{{ $p->name }} (Numéro de lot: {{ $p->sku }})</option>
      @endforeach
    </select>
  </div>
  <div><label class="block text-sm">Nom du lot</label><input name="name" value="{{ old('name',$batch->name) }}" class="w-full border rounded px-2 py-1" required></div>
  <div><label class="block text-sm">Lieu</label>
    <select name="location_id" class="w-full border rounded px-2 py-1" required>
      @foreach($locations as $l)
        <option value="{{ $l->id }}" @selected($batch->location_id==$l->id)>{{ $l->name }}</option>
      @endforeach
    </select>
  </div>
  <div><label class="block text-sm">Quantité</label><input type="number" name="quantity" min="0" value="{{ old('quantity',$batch->quantity) }}" class="w-full border rounded px-2 py-1" required></div>
  <div><label class="block text-sm">Date de péremption</label><input type="date" name="expiry_date" value="{{ optional($batch->expiry_date)->format('Y-m-d') }}" class="w-full border rounded px-2 py-1"></div>
  <button class="bg-avss78-accent text-white px-3 py-1 rounded">Enregistrer</button>
</form>
@endsection
