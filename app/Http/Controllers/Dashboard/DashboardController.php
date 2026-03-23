<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BlastingCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // 🔹 Stats (fix field name)
        $totalRecipient = BlastingCampaign::sum('total_recipient');
        $totalSent = BlastingCampaign::sum('sent_count');
        $totalFailed = BlastingCampaign::sum('failed_count');

        $stats = [
            'total_campaign' => BlastingCampaign::count(),
            'sent' => $totalSent,
            'failed' => $totalFailed,
            'pending' => max(0, $totalRecipient - $totalSent - $totalFailed),
        ];

        // 🔹 Campaign list (mapping ke frontend)
        $campaigns = BlastingCampaign::select(
            'id',
            'name',
            'status',
            'sent_count',
            'failed_count',
            'total_recipient',
            'created_at'
        )
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'status' => $c->status,

                    // 🔥 mapping penting
                    'sent_count' => $c->sent_count,
                    'failed_count' => $c->failed_count,
                    'total' => $c->total_recipient,

                    // 🔥 helper
                    'progress' => $c->total_recipient > 0
                        ? round(($c->sent_count / $c->total_recipient) * 100, 2)
                        : 0,

                    'created_at' => $c->created_at->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'campaigns' => $campaigns,
        ]);
    }
}
