<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class GrievanceController extends Controller
{
	public function grievancelist()
	{
		return view('grievance.grievancelist');
	}

	public function getData()
	{
		$session = session('charge');


		$data = DB::table('audit.grievanceticket as gt')
			->leftJoin('audit.fileuploaddetail as f', 'f.fileuploadid', '=', 'gt.fileuploadid')
			->leftJoin('audit.mst_grievancecategory as gc', 'gc.grievancecatid', '=', 'gt.grievancecatid')
			->leftJoin('audit.mst_dept as md', 'md.deptcode', '=', 'gt.deptcode')
			->select(
				'gt.grievanceticketid',
				'gt.username',
				'gt.email',
				'gt.mobilenumber',
				'gt.deptcode',
				'gt.grievancecatid',
				'gt.description',
				'gt.fileuploadid',
				'f.filename',
				'gc.grievancecatename as categoryname',
				'md.deptelname as department',
				DB::raw("to_char(gt.createdon,'dd-MM-yyyy HH24:MI') as createdon")
			)
			->where('deptcode',  $session->deptcode)
			->orderByDesc('gt.grievanceticketid')
			->get();

		$data->transform(function ($row) {

			$row->department = $row->department ?? '-';

			$row->categoryname = $row->categoryname ?? '-';

			return $row;
		});

		return response()->json($data);
	}

	public function download($id)
	{
		$file = DB::table('audit.fileuploaddetail')
			->where('fileuploadid', $id)
			->first();

		if (!$file) {
			abort(404);
		}

		$path = storage_path('app/' . $file->filepath);

		if (!file_exists($path)) {
			abort(404, 'File not found');
		}

		return response()->download($path, $file->filename);
	}
}
