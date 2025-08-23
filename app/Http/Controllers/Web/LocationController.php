<?php

namespace App\Http\Controllers\Web;

use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController
{
    public function index(): View
    {
        $locations = Location::orderBy('id')->get();
        return view('locations.index', compact('locations'));
    }

    public function create(): View
    {
        return view('locations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:255','unique:locations,name'],
        ]);
        Location::create($data);
        return redirect()->route('locations.index')->with('status', 'Lieu créé');
    }

    public function edit(int $id): View
    {
        $location = Location::findOrFail($id);
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $location = Location::findOrFail($id);
        $data = $request->validate([
            'name' => ['required','string','max:255','unique:locations,name,'.$location->id],
        ]);
        $location->update($data);
        return redirect()->route('locations.index')->with('status', 'Lieu mis à jour');
    }

    public function destroy(int $id): RedirectResponse
    {
        $location = Location::findOrFail($id);
        $location->delete();
        return back()->with('status', 'Lieu supprimé');
    }
}
