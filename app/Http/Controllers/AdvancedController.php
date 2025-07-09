<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Launchpad as LaunchpadResource;
use App\Models\Launchpad;
use App\Models\Rate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdvancedController extends Controller
{
    /**
     * Display the advanced page with launchpads data.
     * @return \Illuminate\View\View
     */
    public function advanced(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query = Launchpad::query()
            ->with(['factory'])
            ->withSum(['trades as volume24h' => fn($q) => $q->where('created_at', '>=', now()->subDays(1))], 'usd')
            ->withCount(['trades']);
        
        if (!empty($keyword)) {
            $query->where('contract', 'LIKE', "%$keyword%")
                ->orWhere('token', 'LIKE', "%$keyword%")
                ->orWhere('name', 'LIKE', "%$keyword%")
                ->orWhere('symbol', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->orWhere('website', 'LIKE', "%$keyword%");
        }
        
        $launchpadsItems = $query->latest('volume24h')->paginate($perPage);
        
        return Inertia::render('Advanced/Advanced', [
            'launchpads' => LaunchpadResource::collection($launchpadsItems),
            'type' => 'advanced',
            'top' => function () {
                return $this->getTopLaunchpads();
            },
            'usdRates' => function () {
                return Rate::all()->keyBy('symbol');
            }
        ]);
    }

    /**
     * Get top launchpads by volume
     */
    private function getTopLaunchpads(int $limit = 10)
    {
        // Create a subquery for the volume aggregation
        $volumeQuery = \DB::table('launchpads as l')
            ->select([
                'l.id',
                'l.name',
                'l.symbol',
                'l.logo',
                'l.contract',
                'l.created_at',
                \DB::raw('SUM(t.usd) as volume'),
                \DB::raw('MAX(t.usd) as high'),
                \DB::raw('MIN(t.usd) as low')
            ])
            ->leftJoin('trades as t', 'l.id', '=', 't.launchpad_id')
            ->groupBy('l.id', 'l.name', 'l.symbol', 'l.logo', 'l.contract', 'l.created_at');

        // Use the subquery to apply the window function
        return \DB::table(\DB::raw("({$volumeQuery->toSql()}) as volumes"))
            ->mergeBindings($volumeQuery)
            ->select([
                'id',
                'name',
                'symbol',
                'contract',
                'logo',
                'low',
                'high',
                'created_at',
                'volume as total_volume',
                \DB::raw('DENSE_RANK() OVER (ORDER BY volume DESC) as position')
            ])
            ->orderBy('volume', 'desc')
            ->limit($limit)
            ->get();
    }
}
