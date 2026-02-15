<?php

namespace App\Services;

use App\Models\EmailSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmailScheduleService
{
    /*
    |--------------------------------------------------------------------------
    | PAGINATE
    |--------------------------------------------------------------------------
    */
    public function paginate(int $perPage = 10)
    {
        return EmailSchedule::query()
            ->with('template')
            ->latest()
            ->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE SCHEDULE
    |--------------------------------------------------------------------------
    */
    public function create(array $data): EmailSchedule
    {
        return DB::transaction(function () use ($data) {

            $schedule = EmailSchedule::create([
                'email_template_id' => $data['template_id'],
                'schedule_at'       => $data['schedule_at'],
                'total_recipient'   => count($data['recipients']),
                'status'            => 'scheduled',
                'created_by'        => Auth::id(),
            ]);

            $schedule->recipients()->createMany(
                collect($data['recipients'])
                    ->map(fn ($email) => [
                        'email' => $email,
                    ])
                    ->toArray()
            );

            return $schedule;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | FIND DETAIL
    |--------------------------------------------------------------------------
    */
    public function findWithRelations(EmailSchedule $schedule): EmailSchedule
    {
        return $schedule->load(['template', 'recipients']);
    }

    /*
    |--------------------------------------------------------------------------
    | PAUSE
    |--------------------------------------------------------------------------
    */
    public function pause(EmailSchedule $schedule): EmailSchedule
    {
        if (!in_array($schedule->status, ['scheduled', 'running'])) {
            throw new \Exception('Schedule tidak bisa dipause');
        }

        $schedule->update([
            'status' => 'paused',
        ]);

        return $schedule;
    }

    /*
    |--------------------------------------------------------------------------
    | RESUME
    |--------------------------------------------------------------------------
    */
    public function resume(EmailSchedule $schedule): EmailSchedule
    {
        if ($schedule->status !== 'paused') {
            throw new \Exception('Schedule tidak bisa dilanjutkan');
        }

        $schedule->update([
            'status' => 'scheduled',
        ]);

        return $schedule;
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */
    public function cancel(EmailSchedule $schedule): EmailSchedule
    {
        if ($schedule->status === 'completed') {
            throw new \Exception('Schedule sudah selesai');
        }

        $schedule->update([
            'status' => 'cancelled',
        ]);

        return $schedule;
    }
}