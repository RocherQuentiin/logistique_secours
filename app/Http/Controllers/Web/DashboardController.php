<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Batch;
use App\Models\Location;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $useMock = request()->boolean('mock');
    $locationId = request()->integer('location_id');
    $period = (int) request()->get('period', 60);
    $allowed = [30, 60, 90, 180];
    if (!in_array($period, $allowed, true)) { $period = 60; }

        if ($useMock) {
            $locations = Location::orderBy('name')->get(['id','name']);
            $today = Carbon::today();
            $months = [];
            $cursor = $today->copy()->startOfMonth();
            $monthsCount = max(3, min(6, (int) ceil($period / 30)));
            for ($i = 0; $i < $monthsCount; $i++) { $months[] = $cursor->copy(); $cursor->addMonth(); }

            $sampleByLoc = collect([
                ['label' => 'Dépôt A', 'qty' => 1200],
                ['label' => 'Dépôt B', 'qty' => 870],
                ['label' => 'Camion 1', 'qty' => 420],
                ['label' => 'Camion 2', 'qty' => 260],
                ['label' => 'Gymnase', 'qty' => 1125],
            ]);
            if ($locationId) {
                $locName = optional($locations->firstWhere('id', $locationId))->name ?? 'Lieu sélectionné';
                $byLocation = collect([[ 'label' => $locName, 'qty' => 600 ]]);
            } else {
                $byLocation = $sampleByLoc;
            }

            $expCounts = [3, 7, 5, 11, 6, 9];
            $expirations = collect($months)->values()->map(function (Carbon $m, $i) use ($expCounts) {
                return [ 'label' => $m->isoFormat('MMM YYYY'), 'count' => $expCounts[$i] ?? 0 ];
            });

            return view('home', [
                'kpis' => [
                    'Produits' => 42,
                    'Lots' => 128,
                    'Quantité totale' => 3875,
                    'Péremptions ≤ 60j' => 9,
                ],
                'byLocation' => $byLocation,
                'topProducts' => collect([
                    ['label' => 'Gants nitrile', 'qty' => 950],
                    ['label' => 'Masques FFP2', 'qty' => 720],
                    ['label' => 'Sérum phy 500ml', 'qty' => 520],
                    ['label' => 'Bandages 10cm', 'qty' => 480],
                    ['label' => 'Garrots', 'qty' => 360],
                ]),
                'expirations' => $expirations,
                'mock' => true,
                'filters' => [ 'location_id' => $locationId, 'period' => $period ],
                'locations' => $locations,
            ]);
        }

        // Real data with filters
        $base = Batch::query();
        if ($locationId) { $base->where('location_id', $locationId); }
        $productCount = (clone $base)->distinct('product_id')->count('product_id');
        if (!$locationId && $productCount === 0) { $productCount = Product::count(); }
        $batchCount = (clone $base)->count();
        $totalQty = (int) (clone $base)->sum('quantity');

        $today = Carbon::today();
        $until = Carbon::today()->addDays($period);
        $expiringSoon = (clone $base)->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $until])
            ->count();

        // Quantities by location
        if ($locationId) {
            $sum = (clone $base)->sum('quantity');
            $locName = optional(Location::find($locationId))->name ?? 'Non défini';
            $byLocation = collect([[ 'label' => $locName, 'qty' => (int) $sum ]]);
        } else {
            $byLocation = Batch::selectRaw('location_id, SUM(quantity) as qty')
                ->groupBy('location_id')
                ->with('location:id,name')
                ->get()
                ->map(fn($r) => [
                    'label' => $r->location?->name ?? 'Non défini',
                    'qty' => (int) $r->qty,
                ]);
        }

        // Top 5 products by total quantity
        $topProducts = (clone $base)
            ->selectRaw('product_id, SUM(quantity) as qty')
            ->groupBy('product_id')
            ->with('product:id,name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'label' => $r->product?->name ?? 'Non défini',
                'qty' => (int) $r->qty,
            ]);

        // Expirations by month (next 6 months)
        $months = [];
        $cursor = $today->copy()->startOfMonth();
        $monthsCount = max(3, min(6, (int) ceil($period / 30)));
        for ($i = 0; $i < $monthsCount; $i++) {
            $months[] = $cursor->copy();
            $cursor->addMonth();
        }
        $expirations = collect($months)->map(function (Carbon $m) use ($base) {
            $start = $m->copy();
            $end = $m->copy()->endOfMonth();
            $count = (clone $base)->whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [$start, $end])
                ->count();
            return [
                'label' => $m->isoFormat('MMM YYYY'),
                'count' => $count,
            ];
        });

        // If database is empty (no products & no batches), offer mock preview instead
        if (!$locationId && $productCount === 0 && $batchCount === 0) {
            return redirect()->to('/?mock=1');
        }

        $locations = Location::orderBy('name')->get(['id','name']);
        return view('home', [
            'kpis' => [
                'Produits' => $productCount,
                'Lots' => $batchCount,
                'Quantité totale' => $totalQty,
                'Péremptions ≤ 60j' => $expiringSoon,
            ],
            'byLocation' => $byLocation,
            'topProducts' => $topProducts,
            'expirations' => $expirations,
            'mock' => false,
            'filters' => [ 'location_id' => $locationId, 'period' => $period ],
            'locations' => $locations,
        ]);
    }
}
