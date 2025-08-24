@extends('layouts.app')
@section('title','Modifier lieu')
@section('content')
<h2 class="text-xl font-semibold mb-4">Modifier lieu</h2>
<form method="POST" action="{{ route('locations.update',$location->id) }}" class="space-y-4 max-w-md">@csrf @method('PUT')
  <div>
    <label class="label">Nom</label>
    <input name="name" value="{{ old('name',$location->name) }}" class="input" required>
  </div>
  <button class="btn btn-primary">Enregistrer</button>
</form>
@endsection
