<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserRoleController
{
    public function index(Request $request): View
    {
        $users = User::select(['id','name','email','role','created_at'])->orderBy('id')->get();
        return view('users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8'],
            'role' => ['required','in:user,admin,dev'],
        ]);

        // Seuls admin et dev peuvent créer; middleware le garantit déjà, mais on garde une sécurité applicative légère
        $creator = $request->user();
        $rank = ['user' => 1, 'admin' => 2, 'dev' => 3];
        $creatorRank = $rank[$creator->role] ?? 0;
        $targetRank = $rank[$data['role']] ?? 99;
        if ($targetRank > $creatorRank) {
            return back()->withErrors(['role' => "Vous ne pouvez pas attribuer un rôle supérieur au vôtre."])->withInput();
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // hashed by cast
            'role' => $data['role'],
            'email_verified_at' => now(),
        ]);

        return back()->with('status', 'Utilisateur créé');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required','in:user,admin,dev'],
        ]);
        $user = User::findOrFail($id);

        // Empêcher d'attribuer un rôle supérieur à celui de l'éditeur
        $editor = $request->user();
        $rank = ['user' => 1, 'admin' => 2, 'dev' => 3];
        $editorRank = $rank[$editor->role] ?? 0;
        $targetRank = $rank[$data['role']] ?? 99;
        if ($targetRank > $editorRank) {
            return back()->withErrors(['role' => "Vous ne pouvez pas attribuer un rôle supérieur au vôtre."]); 
        }

        $user->role = $data['role'];
        $user->save();
        return back()->with('status', 'Rôle mis à jour');
    }
}
