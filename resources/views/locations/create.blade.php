@extends('layouts.app')
@section('title','Nouveau lieu')
@section('content')
<h2 class="text-xl font-semibold mb-4">Nouveau lieu</h2>
<form method="POST" action="{{ route('locations.store') }}" class="space-y-4 max-w-md">@csrf
  <div><label class="block text-sm">Nom</label><input name="name" value="{{ old('name') }}" class="w-full border rounded px-2 py-1" required></div>
  <button class="bg-avss78-accent text-white px-3 py-1 rounded">Créer</button>
</form>
@endsection
