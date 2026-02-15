<?php

namespace App\Http\Controllers\EmailSchedule;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailSchedule\StoreEmailScheduleRequest;
use App\Http\Resources\EmailSchedule\EmailScheduleResource;
use App\Models\BlastingTemplate;
use App\Models\EmailSchedule;
use App\Services\EmailScheduleService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailScheduleController extends Controller
{
    protected EmailScheduleService $service;

    public function __construct(EmailScheduleService $service)
    {
        $this->service = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $schedules = $this->service->paginate(10);

        return Inertia::render('email/schedule/Index', [
            'schedules' => EmailScheduleResource::collection($schedules),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $templates = BlastingTemplate::query()
            ->select('id', 'name')
            ->where('is_active', true)
            ->get();

        return Inertia::render('email/schedule/Create', [
            'templates' => $templates,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(StoreEmailScheduleRequest $request)
    {
        try {
            $this->service->create($request->validated());

            return redirect()
                ->route('email.schedules.index')
                ->with('success', 'Email schedule berhasil dibuat');

        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show(EmailSchedule $schedule)
    {
        $schedule = $this->service->findWithRelations($schedule);

        return Inertia::render('email/schedule/Show', [
            'schedule' => new EmailScheduleResource($schedule),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PAUSE
    |--------------------------------------------------------------------------
    */
    public function pause(EmailSchedule $schedule)
    {
        try {
            $this->service->pause($schedule);

            return back()->with('success', 'Schedule berhasil dipause');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RESUME
    |--------------------------------------------------------------------------
    */
    public function resume(EmailSchedule $schedule)
    {
        try {
            $this->service->resume($schedule);

            return back()->with('success', 'Schedule berhasil dilanjutkan');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */
    public function cancel(EmailSchedule $schedule)
    {
        try {
            $this->service->cancel($schedule);

            return back()->with('success', 'Schedule berhasil dibatalkan');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }
}