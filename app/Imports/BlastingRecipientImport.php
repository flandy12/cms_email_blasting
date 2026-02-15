<?php

namespace App\Imports;

use App\Models\BlastingRecipient;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BlastingRecipientImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new BlastingRecipient([
            'name'     => $row['name'] ?? null,
            'email'    => $row['email'] ?? null,
            'metadata' => $row['meta'] ?? null,
        ]);
    }
}
