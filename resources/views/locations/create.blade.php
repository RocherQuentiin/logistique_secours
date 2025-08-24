@extends('layouts.app')
@section('title','Nouveau lieu')
@section('content')
<h2 class="text-xl font-semibold mb-4">Nouveau lieu</h2>
<form method="POST" action="{{ route('locations.store') }}" class="space-y-4 max-w-md">@csrf
  <div>
    <label class="label">Nom</label>
    <input name="name" value="{{ old('name') }}" class="input" required>
  </div>
  <button class="btn btn-primary">Créer</button>
</form>
@endsection
