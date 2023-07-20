<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessFiles;

use Illuminate\Http\Request;

class ProcessFilesController extends Controller
{
    public function index()
    {
        $filePath = public_path('challenge.json');
        ProcessFiles::dispatch($filePath);
        return response()->json(['message' => 'JSON import job has been dispatched']);
    }
}