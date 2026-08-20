<?php

// app/Http/Controllers/FileController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class FileController extends Controller
{
    public function openFile($filePath)
    {
        // Ensure the file path is correct and the file exists
        $fullPath = storage_path('app/public/' . $filePath);  // Assuming the files are stored in 'storage/app/public'
        // If the file doesn't exist, return a 404 error
        if (!File::exists($fullPath)) {
            abort(404, 'File not found');
        }

        // Return the file as a response for opening it in the browser
        return Response::file($fullPath);
    }
	public function downloadFile(Request $request)
    {
        $filePath = urldecode($request->query('filepath'));

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath);
    }
 
}
?>
