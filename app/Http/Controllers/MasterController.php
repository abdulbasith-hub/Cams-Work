<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\View;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use DB;

class MasterController extends Controller
{
    public function saveDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        try {
            DB::table('departments')->insert([
                'name' => $request->name,
                'description' => $request->description,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Department saved successfully.',
                'redirect_url' => url('/dashboard')
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to save department. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveRegion(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        try {
            DB::table('departments')->insert([
                'name' => $request->name,
                'description' => $request->description,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Department saved successfully.',
                'redirect_url' => url('/dashboard')
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to save department. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function saveState(Request $request)
    {
        $request->validate([
            'state_name' => 'required',
            'state_t_name' => 'required',
            'status' => 'required',
        ]);
        $lastStateCode = DB::table('audit.mst_state')->max('statecode') ?? 0; // Get the maximum statecode or default to 0 if no records exist
        $newStateCode = $lastStateCode + 1;
        try {
            DB::table('audit.mst_state')->insert([
                'statecode' => $newStateCode,
                'stateename' => $request->state_name,
                'statetname' => $request->state_t_name,
                'statusflag' => $request->status,
                'createdon' => now(),
                'updatedon' => now()
            ]);
            return response()->json([
                'success' => true,
                'message' => 'State saved successfully.',
                'redirect_url' => url('/state')
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to save State. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function saveDistrict(Request $request)
    {
        $request->validate([
            'dist_name' => 'required',
            'dist_t_name' => 'required',
            'status' => 'required',
        ]);
        dd($request->all());
        try {
            DB::table('audit.mst_district')->insert([
                'statecode' => $newStateCode,
                'disetname' => $request->dist_name,
                'disttname' => $request->dist_t_name,
                'statusflag' => $request->status,
                'createdon' => now(),
                'updatedon' => now()
            ]);
            return response()->json([
                'success' => true,
                'message' => 'district saved successfully.',
                'redirect_url' => url('/district')
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to save district. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function viewDistrict()
    {
        $state = DB::table('audit.mst_state')
            ->where('statusflag', 'Y')
            ->get();
        return view('pages.district', compact('state'));
    }
    public function getStatesList(Request $request)
    {
        // Join states and districts based on state and district structure in your database
        $query = DB::table('audit.mst_state as s')
            ->where('s.statusflag', 'Y');

        return DataTables::of($query)->addIndexColumn()->addColumn('action', function ($row) {
            return '<button type="button" onclick="openEditModal(' . $row->stateid . ')" class="btn btn-sm btn-primary">
                    <i class="fa fa-pencil"></i> Edit</button>';
        })
            ->rawColumns(['action'])->make(true);
    }

    public function getdistsList(Request $request)
    {
        // Join states and districts based on state and district structure in your database
        $query = DB::table('audit.mst_district')
            ->where('statusflag', 'Y');

        return DataTables::of($query)->addIndexColumn()
            ->addColumn('statecode', function ($row) {
                $state = DB::table('audit.mst_state')
                    ->where('statusflag', 'Y')
                    ->where('statecode', $row->statecode)
                    ->first();

                return $state ? $state->stateename : 'N/A';
            })
            ->addColumn('action', function ($row) {
                return '<button type="button" onclick="openEditModal(' . $row->distid . ')" class="btn btn-sm btn-primary">
                    <i class="fa fa-pencil"></i> Edit</button>';
            })
            ->rawColumns(['action'])->make(true);
    }
}
