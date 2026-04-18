<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\JenisHak;
use App\Models\JenisHakAdat;
use App\Models\Kategori;
use App\Models\PenggunaanRDTR;
use App\Models\StatusKesesuaian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $data['filters'] = [
            'kategori' => Kategori::get(),
            'penggunaan' => PenggunaanRDTR::get(),
            'JenisHak' => JenisHak::get(),
            'JenisHakAdat' => JenisHakAdat::get(),
            'StatusKesesuaian' => StatusKesesuaian::get(),
        ];

        return view('dashboard.peta.index', compact('data'));
    }

    public function overview(Request $request): View
    {
        return view('dashboard.overview');
    }

    public function monitoring(Request $request): View
    {
        return view('dashboard.monitoring');
    }

    private function getUserStats(): array
    {
        $now = Carbon::now();

        $lastMonthStart = $now->copy()->subMonth()->startOfMonth()->startOfDay();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth()->endOfDay();

        $statConfigs = [
            'total_pengguna' => [
                'label' => 'Total Pengguna',
                'color' => 'emerald',
                'query' => User::query(),
            ],
            'pengguna_aktif' => [
                'label' => 'Pengguna Aktif',
                'color' => 'blue',
                'query' => User::where('status', '1'),
            ],
            'pengguna_terverifikasi' => [
                'label' => 'Pengguna Terverifikasi',
                'color' => 'blue',
                'query' => User::whereNotNull('email_verified_at'),
            ],
        ];

        $stats = [];

        foreach ($statConfigs as $key => $config) {
            $query = $config['query'];

            // Total sampai sekarang
            $total = $query->count();

            // Total sampai akhir bulan lalu
            $lastMonthTotal = (clone $query)
                ->where('created_at', '<=', $lastMonthEnd)
                ->count();

            $diff = $total - $lastMonthTotal;

            $growth = 0;
            if ($lastMonthTotal > 0) {
                $growth = round(($diff / $lastMonthTotal) * 100, 1);
            } elseif ($total > 0) {
                $growth = 100;
            }

            $stats[] = [
                'label' => $config['label'],
                'total' => $total,
                'diff' => $diff,
                'growth' => abs($growth),
                'is_up' => $growth >= 0,
                'last_month' => $lastMonthTotal,
                'color' => $config['color'],
            ];
        }

        return $stats;
    }

    private function getUserGrowthChart(): array
    {
        $start = now()->subMonths(11)->startOfMonth();
        $months = collect(range(0, 11))->map(fn ($i) =>
            $start->copy()->addMonths($i)
        );

        $rawData = User::selectRaw("
                DATE_TRUNC('month', created_at) as month,
                COUNT(*) as total
            ")
            ->where('created_at', '>=', $start)
            ->groupBy('month')
            ->pluck('total', 'month');

        return [
            'labels' => $months->map(fn ($m) => $m->format('M Y')),
            'series' => [
                [
                    'name' => 'Users',
                    'data' => $months->map(fn ($m) =>
                        $rawData[$m->startOfMonth()->toDateTimeString()] ?? 0
                    )->values(),
                ]
            ],
        ];
    }
}
