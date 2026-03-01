<?php

namespace App\Services;

use App\Models\BlastingRecipient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BlastingRecipientService
{
    /* =========================
       PAGINATE + SEARCH
    ========================= */
    public function paginate(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return BlastingRecipient::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /* =========================
       CREATE
    ========================= */
    public function create(array $data): BlastingRecipient
    {
        return DB::transaction(function () use ($data) {
            return BlastingRecipient::create([
                'email'     => $data['email'],
                'name'      => $data['name'] ?? null,
                'metadata'  => $data['metadata'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);
        });
    }

    /* =========================
       UPDATE
    ========================= */
    public function update(BlastingRecipient $recipient, array $data): BlastingRecipient
    {
        return DB::transaction(function () use ($recipient, $data) {
            $recipient->update([
                'email'     => $data['email'],
                'name'      => $data['name'] ?? null,
                'metadata'  => $data['metadata'] ?? null,
                'is_active' => $data['is_active'],
            ]);

            return $recipient;
        });
    }

    /* =========================
       DELETE
    ========================= */
    public function delete(BlastingRecipient $recipient): void
    {
        $recipient->delete();
    }

    /* =========================
       TOGGLE STATUS
    ========================= */
    public function toggleStatus(BlastingRecipient $recipient): BlastingRecipient
    {
        $recipient->update([
            'is_active' => ! $recipient->is_active,
        ]);

        return $recipient;
    }

    /* =========================
       BULK INSERT (OPTIONAL)
    ========================= */
    public function bulkInsert(array $rows): void
    {
        DB::transaction(function () use ($rows) {
            BlastingRecipient::insert($rows);
        });
    }

    public function findOrFail(int $id): BlastingRecipient
    {
        return BlastingRecipient::findOrFail($id);
    }

    public function deleteall(): void
    {
        DB::transaction(function () {
            DB::table('blasting_campaign_recipient')->delete();

            DB::table('blasting_recipients')->delete();

            DB::table('blasting_campaigns')->delete();
        });

        return;
    }
}
