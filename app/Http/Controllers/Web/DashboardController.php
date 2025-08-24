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

        if ($useMock) {
            $today = Carbon::today();
            $months = [];
            $cursor = $today->copy()->startOfMonth();
            for ($i = 0; $i < 6; $i++) { $months[] = $cursor->copy(); $cursor->addMonth(); }

            return view('home', [
                'kpis' => [
                    'Produits' => 42,
                    'Lots' => 128,
                    'Quantité totale' => 3875,
                    'Péremptions ≤ 60j' => 9,
                ],
                'byLocation' => collect([
                    ['label' => 'Dépôt A', 'qty' => 1200],
                    ['label' => 'Dépôt B', 'qty' => 870],
                    ['label' => 'Camion 1', 'qty' => 420],
                    ['label' => 'Camion 2', 'qty' => 260],
                    ['label' => 'Gymnase', 'qty' => 1125],
                ]),
                'topProducts' => collect([
                    ['label' => 'Gants nitrile', 'qty' => 950],
                    ['label' => 'Masques FFP2', 'qty' => 720],
                    ['label' => 'Sérum phy 500ml', 'qty' => 520],
                    ['label' => 'Bandages 10cm', 'qty' => 480],
                    ['label' => 'Garrots', 'qty' => 360],
                ]),
                'expirations' => collect($months)->map(fn(Carbon $m, $i) => [
                    'label' => $m->isoFormat('MMM YYYY'),
                    'count' => [3, 7, 5, 11, 6, 9][$i] ?? 0,
                ]),
                'mock' => true,
            ]);
        }

        // Real data
        $productCount = Product::count();
        $batchCount = Batch::count();
        $totalQty = (int) Batch::sum('quantity');

        $today = Carbon::today();
        $in60 = Carbon::today()->addDays(60);
        $expiringSoon = Batch::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $in60])
            ->count();

        // Quantities by location
        $byLocation = Batch::selectRaw('location_id, SUM(quantity) as qty')
            ->groupBy('location_id')
            ->with('location:id,name')
            ->get()
            ->map(fn($r) => [
                'label' => $r->location?->name ?? 'Non défini',
                'qty' => (int) $r->qty,
            ]);

        // Top 5 products by total quantity
        $topProducts = Batch::selectRaw('product_id, SUM(quantity) as qty')
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
        for ($i = 0; $i < 6; $i++) {
            $months[] = $cursor->copy();
            $cursor->addMonth();
        }
        $expirations = collect($months)->map(function (Carbon $m) {
            $start = $m->copy();
            $end = $m->copy()->endOfMonth();
            $count = Batch::whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [$start, $end])
                ->count();
            return [
                'label' => $m->isoFormat('MMM YYYY'),
                'count' => $count,
            ];
        });

        // If database is empty, offer mock preview instead
        if ($productCount === 0 && $batchCount === 0) {
            return redirect()->to('/?mock=1');
        }

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
        ]);
    }
}
