<?php

namespace App\Services;

use App\Imports\UserImport;
use App\Models\BlastJob;
use App\Models\EmailTemplate;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

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

    public function deleteDataBlast()
    {
        BlastJob::truncate();
    }

    public function getTemplates()
    {
        $data = EmailTemplate::paginate(5);
        return $data;
    }

    public function storeTemplate($data)
    {
        // logic to store email template
        $validate = Validator::make($data, [
            'name' => 'required|string|max:255',
            'wording' => 'required|string',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors()->first(),
                'errors'  => $validate->errors(),
            ], 422);
        }
        $results = EmailTemplate::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Template created successfully',
            'data' =>  $results,
        ], 200);
    }
}
