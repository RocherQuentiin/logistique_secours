@extends('layouts.app')

@section('title', 'Gestion des rôles')

@section('content')
<div class="space-y-6">
    <h2 class="text-xl font-semibold">Gestion des rôles</h2>

    @if (session('status'))
        <div class="rounded bg-green-100 text-green-800 px-4 py-2">{{ session('status') }}</div>
    @endif

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left px-4 py-2">#</th>
                    <th class="text-left px-4 py-2">Nom</th>
                    <th class="text-left px-4 py-2">Email</th>
                    <th class="text-left px-4 py-2">Rôle</th>
                    <th class="text-left px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $u)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $u->id }}</td>
                    <td class="px-4 py-2">{{ $u->name }}</td>
                    <td class="px-4 py-2">{{ $u->email }}</td>
                    <td class="px-4 py-2">
                        <form action="{{ route('users.update', $u->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <select name="role" class="border rounded px-2 py-1">
                                <option value="user" @selected($u->role==='user')>user</option>
                                <option value="admin" @selected($u->role==='admin')>admin</option>
                                <option value="dev" @selected($u->role==='dev')>dev</option>
                            </select>
                            <button class="bg-avss78-accent text-white px-3 py-1 rounded">Enregistrer</button>
                        </form>
                    </td>
                    <td class="px-4 py-2 text-gray-500">&nbsp;</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
