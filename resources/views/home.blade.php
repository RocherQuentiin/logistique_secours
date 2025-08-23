@extends('layouts.app')

@section('title', 'Accueil — AVSS78 logistique')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-semibold text-avss78-primary mb-4">Tableau de bord</h2>
        <p class="mb-4">Ceci est une page d'exemple pour vérifier les couleurs et le build Tailwind/Vite.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 border rounded-md">
                <h3 class="font-semibold">Produits</h3>
                <p class="text-sm text-gray-600">12</p>
            </div>
            <div class="p-4 border rounded-md">
                <h3 class="font-semibold">Lots</h3>
                <p class="text-sm text-gray-600">5</p>
            </div>
            <div class="p-4 border rounded-md">
                <h3 class="font-semibold">Alerte stock</h3>
                <p class="text-sm text-red-600">2 articles en dessous du seuil</p>
            </div>
        </div>
    </div>
@endsection
