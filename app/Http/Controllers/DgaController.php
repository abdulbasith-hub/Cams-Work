<?php

namespace App\Http\Controllers;

use App\Models\DgaModel;
use Illuminate\Contracts\View\View;

class DgaController extends Controller
{
    public function index(): View
    {
        return view('dga.home', [
            'content' => DgaModel::homeContent(),
            'departments' => DgaModel::departments(),
        ]);
    }

    public function department(string $slug): View
    {
        $department = DgaModel::departmentContent($slug);

        abort_if(is_null($department), 404);

        return view('dga.department', [
            'department' => $department,
            'departments' => DgaModel::departments(),
        ]);
    }
}
