<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->select(['id','name','email','role','created_at'])
            ->orderBy('id')
            ->get();

        return response()->json(['data' => $users]);
    }

    public function updateRole(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'role' => ['required','in:user,admin,dev'],
        ]);

        $user = User::find($id);
        if (! $user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }

        $user->role = $data['role'];
        $user->save();

        return response()->json(['data' => $user->only(['id','name','email','role'])]);
    }
}
