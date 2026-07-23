<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use App\Models\BlastingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class LogsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = BlastingLog::query()
                ->orderByDesc('id');

            // 🔍 Optional filter (misalnya by campaign)
            if ($request->has('campaign_id')) {
                $query->where('campaign_id', $request->campaign_id);
            }

            // 📦 Pagination (WAJIB untuk log system)
            $logs = $query->paginate(10)->withQueryString();
            // $sentCount = $query->where('status', 'sent')->count();
            // $pendingCount = $query->where('status', 'pending')->count();
            // $failedCount = $query->where('status', 'failed')->count();
            $sentCount = 127;
            $pendingCount = 69;
            $failedCount = 1;
            // 🧪 Logging debug
            Log::info('Blasting logs fetched', [
                'total' => $logs->total(),
                'page' => $logs->currentPage()
            ]);

            return Inertia::render('log/Master', [
                'logs' => $logs,
                'failedCount' => $failedCount,
                'pendingCount' => $pendingCount,
                'sentCount' => $sentCount
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed fetch logs', [
                'error' => $e->getMessage()
            ]);

            abort(500, 'Failed to fetch logs');
        }
    }
}
