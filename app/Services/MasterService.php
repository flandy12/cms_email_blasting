<?php

namespace App\Services;

use App\Imports\UserImport;
use App\Models\BlastJob;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;

class MasterService
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

    public function masterBlast()
    {
        $data = BlastJob::paginate(5);
        return $data;
    }

    public function deleteDataBlast(){
        BlastJob::truncate();
    }
}
