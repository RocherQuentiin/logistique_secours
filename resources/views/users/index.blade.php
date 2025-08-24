@extends('layouts.app')

@section('title', 'Gestion des rôles')

@section('content')
<div class="space-y-6">
    <h2 class="text-xl font-semibold">Gestion des rôles</h2>

    @if (session('status'))
        <div class="rounded bg-green-100 text-green-800 px-4 py-2">{{ session('status') }}</div>
    @endif

    <div class="card p-4">
        <h3 class="font-semibold mb-2">Créer un utilisateur</h3>
        <form method="POST" action="{{ route('users.store') }}" class="grid md:grid-cols-5 gap-3">
            @csrf
            <input class="input" type="text" name="name" placeholder="Nom" value="{{ old('name') }}" required />
            <input class="input" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required />
            <input class="input" type="password" name="password" placeholder="Mot de passe (min 8)" required />
            <select name="role" class="select">
                @php($rank=['user'=>1,'admin'=>2,'dev'=>3])
                @php($me=auth()->user())
                @php($max=$rank[$me->role] ?? 1)
                <option value="user" @selected(old('role')==='user')>user</option>
                @if($max >= 2)
                    <option value="admin" @selected(old('role')==='admin')>admin</option>
                @endif
                @if($max >= 3)
                    <option value="dev" @selected(old('role')==='dev')>dev</option>
                @endif
            </select>
            <button class="btn btn-primary">Créer</button>
        </form>
        @error('role')
            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div class="overflow-x-auto card p-4">
        <table class="data-table text-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th class="col-actions">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $u)
                <tr>
                    <td>{{ $u->id }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>
                        <form action="{{ route('users.update', $u->id) }}" method="POST" class="inline-flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            @php($rank=['user'=>1,'admin'=>2,'dev'=>3])
                            @php($me=auth()->user())
                            @php($max=$rank[$me->role] ?? 1)
                            <select name="role" class="select">
                                <option value="user" @selected($u->role==='user')>user</option>
                                @if($max >= 2)
                                    <option value="admin" @selected($u->role==='admin')>admin</option>
                                @endif
                                @if($max >= 3)
                                    <option value="dev" @selected($u->role==='dev')>dev</option>
                                @endif
                            </select>
                            <button class="btn btn-primary">Enregistrer</button>
                        </form>
                    </td>
                    <td class="text-gray-500">&nbsp;</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
