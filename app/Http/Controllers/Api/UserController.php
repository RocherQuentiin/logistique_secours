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

        // Interdire d'assigner un rôle supérieur à celui de l'appelant
        $caller = $request->user();
        $rank = ['user' => 1, 'admin' => 2, 'dev' => 3];
        $callerRank = $rank[$caller->role] ?? 0;
        $targetRank = $rank[$data['role']] ?? 99;
        if ($targetRank > $callerRank) {
            return response()->json(['message' => 'Rôle supérieur non autorisé'], 403);
        }

        $user->role = $data['role'];
        $user->save();

        return response()->json(['data' => $user->only(['id','name','email','role'])]);
    }
}
