@extends('layouts.app')
@section('title','Modifier lieu')
@section('content')
<div class="card p-5 max-w-xl">
  <div class="form-toolbar">
    <h2 class="text-xl font-semibold">Modifier lieu</h2>
    <a href="{{ route('locations.index') }}" class="btn btn-ghost" title="Retour">⬅️</a>
  </div>
  <form method="POST" action="{{ route('locations.update',$location->id) }}" class="space-y-4">@csrf @method('PUT')
    <div>
      <label class="label">Nom</label>
      <input name="name" value="{{ old('name',$location->name) }}" class="input" required>
      <p class="form-help">Nom court et explicite du lieu.</p>
    </div>
    <div class="divider"></div>
    <div class="flex items-center gap-2">
      <button class="btn btn-primary">Enregistrer</button>
      <a href="{{ route('locations.index') }}" class="btn btn-ghost">Annuler</a>
    </div>
  </form>
</div>
@endsection
