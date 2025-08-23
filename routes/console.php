<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Set a user's role explicitly
Artisan::command('role:set {email} {role}', function (string $email, string $role) {
    $ranks = ['user', 'admin', 'dev'];
    if (! in_array($role, $ranks, true)) {
        $this->error("Rôle invalide. Utiliser: user | admin | dev");
        return 1;
    }
    $user = User::where('email', $email)->first();
    if (! $user) {
        $this->error("Utilisateur introuvable: $email");
        return 1;
    }
    $user->role = $role;
    $user->save();
    $this->info("Rôle mis à jour: {$user->email} -> {$user->role}");
    return 0;
})->purpose('Définir le rôle (user|admin|dev) d\'un utilisateur via email');

// Promote role up the hierarchy (user -> admin -> dev)
Artisan::command('role:promote {email}', function (string $email) {
    $hier = ['user' => 'admin', 'admin' => 'dev', 'dev' => 'dev'];
    $user = User::where('email', $email)->first();
    if (! $user) {
        $this->error("Utilisateur introuvable: $email");
        return 1;
    }
    $from = $user->role ?? 'user';
    $to = $hier[$from] ?? 'user';
    $user->role = $to;
    $user->save();
    $this->info("Promu: {$user->email} {$from} -> {$to}");
    return 0;
})->purpose('Promouvoir un utilisateur (user->admin->dev)');

// Demote role down the hierarchy (dev -> admin -> user)
Artisan::command('role:demote {email}', function (string $email) {
    $hier = ['dev' => 'admin', 'admin' => 'user', 'user' => 'user'];
    $user = User::where('email', $email)->first();
    if (! $user) {
        $this->error("Utilisateur introuvable: $email");
        return 1;
    }
    $from = $user->role ?? 'user';
    $to = $hier[$from] ?? 'user';
    $user->role = $to;
    $user->save();
    $this->info("Rétrogradé: {$user->email} {$from} -> {$to}");
    return 0;
})->purpose('Rétrograder un utilisateur (dev->admin->user)');
