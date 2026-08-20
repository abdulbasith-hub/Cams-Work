<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;

use Illuminate\Http\Request;
use DataTables;

use App\Models\AuditCertificateModel;


class AuditCertificateController extends Controller
{


    public function CreateAuditCertificate(Request $request)
    {
       
           // Validation rules
            $validatedData = $request->validate([
                'membership_sharedcapital' => 'required|numeric|min:0', // Must be a number and non-negative
                'deposits_borrowings' => 'required|numeric|min:0', // Same as above
                'reserves_surplus' => 'required|numeric|min:0',
                'other_liability' => 'required|numeric|min:0',
                'investments' => 'required|numeric|min:0',
                'loans_advances' => 'required|numeric|min:0',
                'trading_result' => 'required|numeric|min:0',
                'net_result' => 'required|numeric|min:0',
            ]);


        try {       
               // Prepare data using $request
                $InsertData = [
                                'membership_sharedcapital' => $request->membership_sharedcapital,
                                'deposits_borrowings' => $request->deposits_borrowings,
                                'reserves_surplus' => $request->reserves_surplus,
                                'other_liability' => $request->other_liability,
                                'investments' => $request->investments,
                                'loans_advances' => $request->loans_advances,
                                'trading_result' => $request->trading_result,
                                'net_result' => $request->net_result,
                                'statusflag'=>'Y'
                            ];
                
                $Insert = AuditCertificateModel::createIfNotExistsOrUpdate($InsertData);

                if (!$Insert) {
                    // If user already exists (based on conditions), return an error
                    return response()->json(['error' => 'Details already exists'], 400);
                }else
                {
                    return response()->json(['success' => 'Audit Certificate Created successfully.']);
                }


        } catch (QueryException $e) {
            // Handle any database-specific exceptions (e.g., duplicate entry)
            Log::error("Database error: " . $e->getMessage());
            throw new Exception("Database error occurred. Please try again later.");
        } catch (Exception $e) {
            // Handle any other general exceptions
            Log::error("General error: " . $e->getMessage());
            throw new Exception("Something went wrong: " . $e->getMessage());
        }




        
    }


    public function FetchAllCertificate()
    {

         // Fetch all users
         $Certificates = AuditCertificateModel::fetchAllusers();

         $Certificates = $Certificates->map(function ($item, $key) {
            $item->Slno = $key + 1; // Add serial number starting from 1
            return $item;

        })->values(); // Reset keys to ensure sequential indexing
         
         return response()->json(['data' => $Certificates]); // Ensure the data is wrapped under "data"

    }

   
}


?>