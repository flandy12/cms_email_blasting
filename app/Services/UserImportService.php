<?php

namespace App\Services;

use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;

class UserImportService
{
    /**
     * Import data user dari file
     *
     * @param UploadedFile $file
     * @return void
     */
    public function import(UploadedFile $file): void
    {
        Excel::import(new UserImport, $file);
    }
}