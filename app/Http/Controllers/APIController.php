<?php
namespace App\Http\Controllers;

use App\Helpers\CryptoHelper;
use App\Helpers\JWTService;
use App\Http\Requests\MasterdesignationRequest;
use App\Models\APIModel;
use App\Models\BaseModel;
use App\Models\MastersModel;
use App\Models\UserManagementModel;
// use Firebase\JWT\JWT;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;
use Exception;

class APIController extends Controller
{
    // ///////////////////////////////////////////////////////////////////////////////////////////////

    public function generateToken(Request $req)
    {
        $username = $req->username;
        $password = $req->password;

        $client = DB::table('audit.mst_apiclient')
            ->where('username', $username)
            // ->where('statusflag', 'Y')
            ->first();

        // exit;
        if (!$client) {
            return response()->json([
                'status' => false,
                'message' => 'Client not found'
            ], 404);
        }

        $validPwd = DB::table('audit.mst_apiclient')
            ->where('username', $username)
            ->where('pwd', $password)
            // ->whereRaw('password_hash = crypt(?, password_hash)', [$password])
            ->first();

        if (!$validPwd) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = JWTService::generateToken($client->apicode);

        return response()->json([
            'status' => true,
            'message' => 'Token generated successfully',
            'client_code' => $client->apicode,
            'token' => $token,
            'expires_in_minutes' => env('JWT_EXPIRE_MINUTES')
        ]);
    }

    public function getslipdetails(Request $request)
    {
        try {
            // Validate request inputs
            $request->validate([
                'erpcode' => 'required',
                'data' => 'required'
            ]);

            $apicode = $request->input('erpcode');
            $encryptedData = $request->input('data');

            // Get secret key
            $getsecretkey = APIModel::getsecretkey($apicode);

            if (!$getsecretkey) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Invalid API code / secret key not found'
                ], 400);
            }

            // Decrypt incoming data
            $jsonData = CryptoHelper::decryptAES256GCM($encryptedData, $getsecretkey);

            if (!$jsonData) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Decryption failed'
                ], 400);
            }

            // Optional: Validate JSON format
            $decoded = json_decode($jsonData, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Invalid JSON format'
                ], 400);
            }

            $data = json_decode($jsonData, true);

            DB::beginTransaction();

            $result = DB::select(
                'SELECT audit.api_slipdetailsinsert(CAST(? AS json)) AS api_slipdetailsinsert',
                [json_encode($data)]
            );
            // Process temporary slip details
            DB::select('SELECT audit.process_temp_slipdetails()');

            DB::commit();

            // return response()->json($result);
            $response = json_decode($result[0]->api_slipdetailsinsert, true);

            return response()->json($response);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'ERROR',
                'message' => 'Database error',
                'details' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'ERROR',
                'message' => 'Unexpected error',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function entrymeetingdata($auditscheduleid)
    {
        try {
            // 1️⃣ Fetch DB data
            $PlanFinalisedData = APIModel::getentrymeetingdel($auditscheduleid);
            //   print_r($PlanFinalisedData);
            //  die;
            if (empty($PlanFinalisedData)) {
                return [
                    'status' => false,
                    'message' => 'No DB data found'
                ];
            }

            $row = (array) $PlanFinalisedData[0];

            // 2️⃣ Decode DB JSON
            $dbData = json_decode($row['api_entrymeetingdel'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'status' => false,
                    'message' => 'Invalid JSON from DB',
                    'error' => json_last_error_msg()
                ];
            }

            // 3️⃣ Extract credentials
            $username = $dbData['username'] ?? null;
            $password = $dbData['password'] ?? null;
            $apiurl = $dbData['apiurl'] ?? null;
            $authtokenurl = $dbData['authtokenurl'] ?? null;
            $apicode = $dbData['apicode'] ?? null;
            $records = $dbData['records'] ?? null;

            if (!$username || !$password || !$apiurl || empty($records)) {
                return [
                    'status' => false,
                    'message' => 'Missing API credentials or records'
                ];
            }

            // 4️⃣ Get Auth Token
            $tokenResponse = Http::timeout(30)
                ->acceptJson()
                ->post($authtokenurl, [
                    'username' => $username,
                    'password' => $password
                ]);

            if (!$tokenResponse->successful()) {
                return [
                    'status' => false,
                    'message' => 'Token API failed',
                    'response' => $tokenResponse->body()
                ];
            }

            $token = $tokenResponse->json()['token'] ?? null;

            if (!$token) {
                return [
                    'status' => false,
                    'message' => 'Token not received'
                ];
            }

            // 5️⃣ Prepare payload
            $payload = [
                'allocationId' => 0,
                'instId' => (string) $records['instId'],
                'auditPlanId' => $records['auditplanid'],
                'auditScheduleId' => $records['auditscheduleid'],
                'finYear' => '2025-2026',
                'currentQuarter' => $records['currentQuarter'],
                'numberOfAuditors' => $records['numberOfAuditors'],
                'pacsId' => $records['pacsId'],
                'reportFileName' => $records['reportfilename'],
                'allocatedBy' => $records['allocatedBy'],
                'startDate' => date('Y-m-d\TH:i:s\Z', strtotime($records['entrymeetingdate'])),
                'endDate' => date('Y-m-d\TH:i:s\Z', strtotime($records['proposedexitmeetingdate'])),
                'auditors' => array_map(function ($aud, $i) {
                    return [
                        'allocationType' => $i === 0 ? 'PRIMARY' : 'SECONDARY',
                        'auditorName' => $aud['auditorName'],
                        'auditorPhoneNumber' => $aud['auditorPhoneNumber'],
                        'auditorEmail' => $aud['auditorEmail'],
                        'auditorDesignation' => $aud['auditorDesignation'],
                        'schteammemberid' => $aud['schteammemberid'],
                        'headQuartersOfAuditor' => 'chennai'
                    ];
                }, $records['auditors'], array_keys($records['auditors']))
            ];

            // 6️⃣ Encrypt payload
            $getsecretkey = APIModel::getsecretkey($apicode);
            $jsonData = json_encode($payload);
            $encryptedData = CryptoHelper::encryptAES256GCM($jsonData, $getsecretkey);
//print_R($jsonData);
//exit;

            $postdata = [
                'data' => $encryptedData
            ];

            // 7️⃣ Call Main API
            $response = Http::asJson()->withToken($token)->post($apiurl, $postdata);



            //  $response = Http::asJson()->withToken($token)->post($apiurl, $encryptedData);

            return ['status' => $response->successful(), 'http_code' => $response->status(), 'response' => $response->json() ?? $response->body(), 'encrpteddata' => $postdata,
'encrypted_payload' => $postdata, 'apiurl' => $apiurl];
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return [
                'status' => false,
                'message' => 'HTTP Request failed',
                'error' => $e->getMessage()
            ];
        } catch (\Throwable $e) {
            return [
                'status' => false,
                'message' => 'Unexpected error',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        }
    }

    public function exitmeetingdel($auditscheduleid)
    {
        try {
            /*---------------------------------------------------------
            1️⃣ Fetch DB Data
            ----------------------------------------------------------*/
            $PlanFinalisedData = APIModel::exitmeetingdel($auditscheduleid);

            if (empty($PlanFinalisedData)) {
                return [
                    'status' => false,
                    'message' => 'No DB data found'
                ];
            }

            $row = $PlanFinalisedData[0]->api_exitmeetingdel ?? null;

            if (empty($row)) {
                return [
                    'status' => false,
                    'message' => 'Missing API data from DB'
                ];
            }

            /*---------------------------------------------------------
            2️⃣ Decode JSON returned from DB
            ----------------------------------------------------------*/
            $dbData = json_decode($row, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'status' => false,
                    'message' => 'Invalid JSON from DB',
                    'error' => json_last_error_msg()
                ];
            }

            /*---------------------------------------------------------
            3️⃣ Extract credentials
            ----------------------------------------------------------*/
            $username = $dbData['username'] ?? null;
            $password = $dbData['password'] ?? null;
            $apicode = $dbData['apicode'] ?? null;
            $apiurl = $dbData['apiurl'] ?? null;
            $authtokenurl = $dbData['authtokenurl'] ?? null;
            $records = $dbData['records'] ?? [];

            if (!$username || !$password || !$apiurl || !$authtokenurl || empty($records)) {
                return [
                    'status' => false,
                    'message' => 'Missing API credentials or records'
                ];
            }

            /*---------------------------------------------------------
            4️⃣ Get Secret Key
            ----------------------------------------------------------*/
            $secretkey = APIModel::getsecretkey($apicode);

            if (!$secretkey) {
                return [
                    'status' => false,
                    'message' => 'Secret key not found'
                ];
            }

            /*---------------------------------------------------------
            5️⃣ Prepare API Payload
            ----------------------------------------------------------*/
            $payload = [
                'financialyear' => $records['financialyear'] ?? null,
                'auditQuarterCode' => $records['auditquartercode'] ?? null,
                'erpCode' => $records['erpno'] ?? null,
                'exitMeetDatewithTime' => $records['exitmeetdatewithtime'] ?? null,
                'noOfDaysForExit' => 5,
                'auditScheduleid' => $records['auditscheduleid'] ?? null,
                'finalExitMeetingDate' => $records['replayenddate'] ?? null
            ];

            /*---------------------------------------------------------
            6️⃣ Convert payload to JSON
            ----------------------------------------------------------*/
            $jsonData = json_encode($payload);

            if ($jsonData === false) {
                return [
                    'status' => false,
                    'message' => 'JSON encoding failed'
                ];
            }

            /*---------------------------------------------------------
            7️⃣ Encrypt Payload (AES256-GCM)
            ----------------------------------------------------------*/
            $encryptedData = CryptoHelper::encryptAES256GCM($jsonData, $secretkey);
            if (!$encryptedData) {
                return [
                    'status' => false,
                    'message' => 'Encryption failed'
                ];
            }

            /*---------------------------------------------------------
            8️⃣ Get AUTH TOKEN
            ----------------------------------------------------------*/
            $tokenResponse = Http::timeout(30)
                ->acceptJson()
                ->post($authtokenurl, [
                    'username' => $username,
                    'password' => $password
                ]);

            if (!$tokenResponse->successful()) {
                return [
                    'status' => false,
                    'message' => 'Auth API failed',
                    'response' => $tokenResponse->body()
                ];
            }

            $token = $tokenResponse->json()['token'] ?? null;

            if (!$token) {
                return [
                    'status' => false,
                    'message' => 'Token not received'
                ];
            }


            $postdata = [
                'data' => $encryptedData
            ];

            /*---------------------------------------------------------
            9️⃣ Call MAIN API
            ----------------------------------------------------------*/
            $response = Http::asJson()
                ->withToken($token)
                ->post($apiurl, $postdata);

            /*---------------------------------------------------------
            🔟 Return Response
            ----------------------------------------------------------*/
            return [
                'status' => $response->successful(),
                'http_code' => $response->status(),
                'response' => $response->json() ?: $response->body(),
'encrypted_payload' => $postdata, 'apiurl' => $apiurl
            ];
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return [
                'status' => false,
                'message' => 'HTTP request failed',
                'error' => $e->getMessage()
            ];
        } catch (\Throwable $e) {
            return [
                'status' => false,
                'message' => 'Unexpected error',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        }
    }

    public function epacslogin(Request $request)
    {
        $request->validate([
            'pacsid' => 'required|string',
            'dccbid' => 'required|string',
            'schteammemberid' => 'required|string',
        ]);

        $token = $this->getAuthToken();

        // $user = UserManagementModel::userdetails();

        $payload = [
            'loginName' => session('user')->username ?? 'AuditorKiran',
            // 'ifhrmsno'  => $user->ifhrmsno,
            // 'schteammemberid' => $request->schteammemberid,
            'stateId' => 30,
            'dccbId' => $request->dccbid,
            'pacsid' => $request->pacsid,
            'loginDate' => now()->format('Y-m-d')
        ];
        //  dd($payload);

        $response = Http::timeout(10)
            ->retry(2, 500)
            ->withToken($token)
            ->post('https://cams.uniteerp.in/api/CAMS/GetUserToken/', $payload);

        if ($response->failed()) {
            logger()->error('EPACS token API failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            abort(500, 'EPACS token generation failed');
        }

        $data = $response->json();

        if (!isset($data['encryption'])) {
            logger()->error('EPACS encryption key missing', $data);
            abort(500, 'Invalid EPACS response');
        }
        //  print_r($data);
        //  die;
        return view('epacs.redirect', [
            'baseUrl' => $data['baseUrl'],
            'encryData' => $data['encryption']
        ]);
    }

    private function getAuthToken()
    {
        $response = Http::post('https://cams.uniteerp.in/api/auth/gettoken/');

        if (!$response->successful()) {
            throw new \Exception('Unable to get CAMS token');
        }

        return $response->json()['token'];
    }

    public function exitmeetdata()
    {
        $auditscheduleid = 14206;
        $apifor = 'X';

        $PlanFinalisedData = APIModel::exitmeetdata($auditscheduleid, $apifor);
        $row = json_decode(json_encode($PlanFinalisedData[0]), true);

        $data = json_decode($row['api_exitmeetdata'], true);

        $apiurl = $data['apiurl'];
        $username = $data['username'];
        $password = $data['password'];
        $datadel = $data['records'];

        if (!$apiurl) {
            return ['status' => false, 'error' => 'API URL missing'];
        }

        $response = Http::timeout(60)
            ->connectTimeout(15)
            ->asJson()
            ->post('http://65.1.39.236/api/pushdata_exitmeetdata', [
                'username' => $username,
                'password' => $password,
                'data' => $datadel,
            ]);

        if (!$response->successful()) {
            return [
                'status' => false,
                'code' => $response->status(),
                'body' => $response->body(),
            ];
        }

        return $response->json();
    }

    public function readyforauditdata(Request $request)
{
try {
// ✅ Validate request
$validated = $request->validate([
'erpcode' => 'required|string|max:2',
'data' => 'required'
]);

$erpcode = trim($validated['erpcode']);
$encryptedData = $validated['data'];

// ✅ Get secret key
$secretKey = APIModel::getsecretkey($erpcode);

if (empty($secretKey)) {
return response()->json([
'status' => 'Error',
'message' => 'Invalid ERP code'
], 401);
}

// ✅ Decrypt data
try {
$jsonData = CryptoHelper::decryptAES256GCM($encryptedData, $secretKey);
} catch (\Throwable $e) {
\Log::warning('Decryption failed', [
'erpcode' => $erpcode,
'error' => $e->getMessage()
]);

return response()->json([
'status' => 'Error',
'message' => 'Invalid encrypted data'
], 400);
}

if (empty($jsonData)) {
return response()->json([
'status' => 'Error',
'message' => 'Decryption failed'
], 400);
}

// ✅ Decode JSON
$data = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
return response()->json([
'status' => 'Error',
'message' => 'Invalid JSON format'
], 400);
}

// ✅ Encode for DB
$payload = json_encode($data, JSON_UNESCAPED_UNICODE);

// ✅ Call DB function
try {
$result = DB::select(
'SELECT audit.temp_readyforaudit(?, ?) AS response',
[$payload, $erpcode]
);
} catch (\Throwable $e) {
\Log::error('DB function error', [
'erpcode' => $erpcode,
'error' => $e->getMessage()
]);

return response()->json([
'status' => 'Error',
'message' => 'Database processing failed'
], 500);
}

$dbResponse = $result[0]->response ?? null;

if (empty($dbResponse)) {
\Log::error('Empty DB response', ['erpcode' => $erpcode]);

return response()->json([
'status' => 'Error',
'message' => 'No response from server'
], 500);
}

// ✅ Decode DB response
$responseData = json_decode($dbResponse, true);

if (json_last_error() !== JSON_ERROR_NONE) {
\Log::error('Invalid JSON from DB', [
'erpcode' => $erpcode,
'response' => $dbResponse
]);

return response()->json([
'status' => 'Error',
'message' => 'Invalid server response'
], 500);
}

// ===============================
// ✅ FINAL RESPONSE (RAW DB DATA)
// ===============================
return response()->json($responseData, 200);

} catch (\Illuminate\Validation\ValidationException $e) {

return response()->json([
'status' => 'Error',
'message' => 'Validation failed',
'errors' => $e->errors()
], 422);

} catch (\Throwable $e) {

\Log::critical('readyforauditdata fatal error', [
'message' => $e->getMessage(),
'line' => $e->getLine(),
'file' => $e->getFile()
]);

return response()->json([
'status' => 'Error',
'message' => 'Internal server error'
], 500);
}
}


public function auditscheduledel($apicode)
{
try {

// 1️⃣ Fetch PostgreSQL function result
$PlanFinalisedData = APIModel::auditscheduledel($apicode);
// print_r($PlanFinalisedData);
// exit;

if (empty($PlanFinalisedData)) {

return response()->json([
'status' => false,
'message' => 'No DB data found'
], 404);
}

// 2️⃣ Convert object to array
$row = (array) $PlanFinalisedData[0];


// 3️⃣ Decode PostgreSQL JSON
$dbData = json_decode($row['api_auditscheduledel'], true);

if (json_last_error() !== JSON_ERROR_NONE) {

return response()->json([
'status' => false,
'message' => 'Invalid JSON from DB',
'error' => json_last_error_msg()
], 500);
}

// 4️⃣ Extract values
$username = $dbData['username'] ?? null;
$password = $dbData['pwd'] ?? null;
$apiurl = $dbData['apiurl'] ?? null;
$authtokenurl = $dbData['authtokenurl'] ?? null;
$secretkey = $dbData['secretkey'] ?? null;
$data = $dbData['data'] ?? [];

// 5️⃣ Validate required fields
if (
empty($apiurl) ||
empty($secretkey) ||
empty($data)
) {

return response()->json([
'status' => false,
'message' => 'Missing API URL / Secret Key / Data'
], 400);
}

// 6️⃣ Send API Request
$response = Http::withHeaders([

'x-api-key' => $secretkey,
'Content-Type' => 'application/json',
'Accept' => 'application/json'

])->post($apiurl, $data);

// 7️⃣ Return response
return response()->json([

'status' => $response->successful(),

'http_code' => $response->status(),

'response' => $response->json() ?? $response->body(),

'apiurl' => $apiurl,

'authtokenurl' => $authtokenurl,

'payload' => $data

], $response->status());
} catch (\Illuminate\Http\Client\RequestException $e) {

return response()->json([

'status' => false,

'message' => 'HTTP Request failed',

'error' => $e->getMessage()

], 500);
} catch (\Throwable $e) {

return response()->json([

'status' => false,

'message' => 'Unexpected error',

'error' => $e->getMessage(),

'line' => $e->getLine(),

'file' => $e->getFile()

], 500);
}
}


public function updateuserdel(Request $request)
    {
        try {

            // =========================================================
            // 1. INPUT
            // =========================================================
            $leaveid = $request->leaveid ?? null;
            $othertransid = $request->othertransactionid ?? null;

            // =========================================================
            // 2. DECRYPT IDS
            // =========================================================
            if ($leaveid) {
                $leaveid = Crypt::decryptString($leaveid);
            }

            if ($othertransid) {
                $othertransid = Crypt::decryptString($othertransid);
            }

            // =========================================================
            // 3. FETCH DB DATA
            // =========================================================
            $getupdateuserdel = APIModel::api_updateuserdel($leaveid, $othertransid);

            if (empty($getupdateuserdel)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No DB data found'
                ], 404);
            }

            $row = (array) $getupdateuserdel[0];
            $dbData = json_decode($row['api_updateuserdel'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid JSON from DB',
                    'error' => json_last_error_msg()
                ], 500);
            }

            // =========================================================
            // 4. VALIDATE API CREDENTIALS
            // =========================================================
            if (empty($dbData['username']) || empty($dbData['pwd']) || empty($dbData['apiurl'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Missing API credentials'
                ], 400);
            }

            // =========================================================
            // 5. GET TOKEN
            // =========================================================
            $tokenResponse = Http::timeout(30)
                ->acceptJson()
                ->post($dbData['authtokenurl'], [
                    'username' => $dbData['username'],
                    'password' => $dbData['pwd']
                ]);

            if (!$tokenResponse->successful()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token API failed',
                    'response' => $tokenResponse->body()
                ], 500);
            }

            $token = $tokenResponse->json('token');

            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token not received'
                ], 500);
            }

            // =========================================================
            // 6. AUDITORS
            // =========================================================
            $auditors = [
                [
                    'allocationType' => 'Lead Auditor',
                    'auditorName' => $dbData['fromuser'] ?? '',
                    'auditorPhoneNumber' => $dbData['fromuser_mobilenumber'] ?? '',
                    'auditorEmail' => $dbData['fromuser_email'] ?? '',
                    'auditorDesignation' => $dbData['fromuser_designation'] ?? '',
                    'schteammemberid' => $dbData['fromuser_schmemberid'] ?? '',
                    'headQuatersOfAuditor' => 'Chennai',
                    'isauditorexit' => true
                ],
                [
                    'allocationType' => 'SECONDARY',
                    'auditorName' => $dbData['touser'] ?? '',
                    'auditorPhoneNumber' => $dbData['touser_mobilenumber'] ?? '',
                    'auditorEmail' => $dbData['touser_email'] ?? '',
                    'auditorDesignation' => $dbData['touser_designation'] ?? '',
                    'schteammemberid' => $dbData['touser_schmemberid'] ?? ''
                ]
            ];

            // =========================================================
            // 7. PAYLOAD
            // =========================================================
            $payload = [
                'allocationId' => 0,
                'instId' => (string) $dbData['instid'],
                'finYear' => $dbData['audityear'] ?? '2025-2026',
                'currentQuarter' => $dbData['planname'] ?? 'Q4',
                'numberOfAuditors' => 2,
                'pacsId' => $dbData['erpno'] ?? '',
                'reportfilename' => $dbData['reportfilename'] ?? '',
                'allocatedBy' => 'Admin',
                'auditPlanId' => $dbData['auditplanid'],
                'auditScheduleId' => $dbData['auditscheduleid'],
                'startDate' => date('Y-m-d\TH:i:s\Z', strtotime($dbData['entrymeetdate'])),
                'endDate' => date('Y-m-d\TH:i:s\Z', strtotime($dbData['proposedexitmeetdate'])),
                'auditors' => $auditors
            ];

            // =========================================================
            // 8. ENCRYPT PAYLOAD
            // =========================================================
            $jsonData = json_encode($payload);

            $secretKey = APIModel::getsecretkey($dbData['apicode']);

            $encryptedData = CryptoHelper::encryptAES256GCM($jsonData, $secretKey);

            $postdata = ['data' => $encryptedData];

            // =========================================================
            // 9. CALL API
            // =========================================================
            $response = Http::asJson()
                ->withToken($token)
                ->post($dbData['apiurl'], $postdata);

            // print_r($response);
            // exit;

            // $response->successful();
            $res = $response->json();

            // print_r($res['data']);
            // exit;

            // =========================================================
            // 10. LOG API RESPONSE
            // =========================================================
            APIModel::apilog([
                'auditscheduleid' => $dbData['auditscheduleid'],
'apiname'=>'U',
                'apiurl' => $dbData['apiurl'],
                'encryptedpayload' => json_encode($postdata),
                'response' => $response->body(),
                'createdon' => now()
            ]);



            // =========================================================
            // 11. SUCCESS CHECK (IMPORTANT PART)
            // =========================================================
            $isSuccess =
                strtoupper(trim($res['status'] ?? '')) === 'SUCCESS'
                && strtoupper(trim($res['data']['status'] ?? '')) === 'SUCCESS';

            if (!$isSuccess) {



                return response()->json([
                    'status' => false,
                    'message' => $res['data']['message'] ?? 'Error'
                ], 500);
            } else {
                APIModel::updateapisent($leaveid, $othertransid);

                return response()->json([
                    'status' => 'success',
                    'message' => $res['data']['message'] ?? 'Error'
                ], 200);
            }
        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => 'Unexpected error',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
	

}
