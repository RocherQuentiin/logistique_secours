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

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required','in:user,admin,dev'],
        ]);
        $user = User::findOrFail($id);
        $user->role = $data['role'];
        $user->save();
        return back()->with('status', 'Rôle mis à jour');
    }
}
