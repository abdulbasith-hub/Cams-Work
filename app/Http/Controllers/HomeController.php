<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\FileUploadService;
use App\Services\SmsService;
use App\Models\SmsmailModel;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;





class HomeController extends Controller
{

	protected $fileUploadService;
	protected $smsService;

	public function __construct(FileUploadService $fileUploadService, SmsService $smsService)
	{
		$this->fileUploadService = $fileUploadService;
		$this->smsService = $smsService;
	}

	private function grievanceOtpEmailKey($mobileNumber)
	{
		return 'grievance_' . $mobileNumber . '@cams.local';
	}

	public function sendGrievanceOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'mobilenumber' => [
				'required',
				'digits:10',
				'regex:/^[6-9][0-9]{9}$/'
			],
		], [
			'mobilenumber.required' => 'Enter Mobile Number',
			'mobilenumber.digits' => 'Mobile Number must be 10 digits.',
			'mobilenumber.regex' => 'Enter valid mobile number.',
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'errors' => $validator->errors()
			], 422);
		}

		$mobileNumber = $request->mobilenumber;
		$otp = 123456;

		$saveResult = SmsmailModel::saveOTP([
			'userid' => 0,
			'email' => $this->grievanceOtpEmailKey($mobileNumber),
			'otp' => $otp,
		]);

		if (($saveResult['status'] ?? null) !== 'success') {
			return response()->json([
				'status' => false,
				'message' => 'Failed to send OTP. Please try again later.'
			], 500);
		}

		// $smsResponse = $this->smsService->sendSMS($mobileNumber, $otp, null, 'login');

		// if (is_array($smsResponse) && isset($smsResponse['status']) && (string) $smsResponse['status'] === '100') {
		// 	session([
		// 		'grievance_mobile_verified' => false,
		// 		'grievance_mobile_number' => $mobileNumber,
		// 	]);

			session([
				'grievance_mobile_verified' => false,
				'grievance_mobile_number' => $mobileNumber,
				'grievance_otp_sent_at' => now()->timestamp,
			]);

			return response()->json([
				'status' => true,
				'message' => 'OTP has been sent successfully.'
			]);
		//}

		return response()->json([
			'status' => false,
			'message' => 'Failed to send OTP. Please try again later.'
		], 500);
	}

	public function verifyGrievanceOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'mobilenumber' => [
				'required',
				'digits:10',
				'regex:/^[6-9][0-9]{9}$/'
			],
			'otp' => 'required|digits:6',
		], [
			'mobilenumber.required' => 'Enter Mobile Number',
			'mobilenumber.regex' => 'Enter valid mobile number.',
			'otp.required' => 'Enter OTP',
			'otp.digits' => 'OTP must be 6 digits.',
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'errors' => $validator->errors()
			], 422);
		}

		$mobileNumber = $request->mobilenumber;
		$otpSentAt = session('grievance_otp_sent_at');

		if (
			session('grievance_mobile_number') !== $mobileNumber ||
			!$otpSentAt ||
			(now()->timestamp - (int) $otpSentAt) > 300
		) {
			session()->forget(['grievance_mobile_verified', 'grievance_otp_sent_at']);

			return response()->json([
				'status' => false,
				'message' => 'OTP expired. Please send OTP again.'
			], 422);
		}

		$isVerified = SmsmailModel::verifyOTP([
			'email' => $this->grievanceOtpEmailKey($mobileNumber),
			'otp' => $request->otp,
		]);

		if ($isVerified === true) {
				session([
					'grievance_mobile_verified' => true,
					'grievance_mobile_number' => $mobileNumber,
				]);
				session()->forget('grievance_otp_sent_at');

			return response()->json([
				'status' => true,
				'message' => 'Mobile Number verified successfully.'
			]);
		}

		return response()->json([
			'status' => false,
			'message' => 'Incorrect OTP.'
		], 422);
	}

	public function clearGrievanceVerification()
	{
		session()->forget([
			'grievance_mobile_verified',
			'grievance_mobile_number',
			'grievance_otp_sent_at'
		]);

		return response()->json([
			'status' => true
		]);
	}


		public function grievanceform_compact()
	{
		$departments = DB::table('audit.mst_dept')
			->where('statusflag', 'Y')
			->orderBy('orderid')
			->select('deptcode', 'deptesname', 'deptelname')
			->get();

		$categories = DB::table('audit.mst_grievancecategory')
			->where('statusflag', 'Y')
			->orderBy('grievancecatename')
			->select('grievancecatid', 'grievancecatename')
			->get();

		return view(
			'grievance.registergrievance',
			compact('departments', 'categories')
		);
	}

		public function grievanceStatus()
		{
			$mobileNumber = session('grievance_mobile_number');

			if (!session('grievance_mobile_verified') || !$mobileNumber) {
				return response()->json([
					'status' => false,
					'message' => 'Please verify the mobile number before checking grievance status.'
				], 422);
			}

			try {
				$department = DB::table('audit.mst_dept')
					->where('statusflag', 'Y')
					->pluck('deptelname', 'deptcode')
					->toArray();

				$category = DB::table('audit.mst_grievancecategory')
					->where('statusflag', 'Y')
					->pluck('grievancecatename', 'grievancecatid')
					->toArray();

				$hasCategoryColumn = DB::table('information_schema.columns')
					->where('table_schema', 'audit')
					->where('table_name', 'grievanceticket')
					->where('column_name', 'grievancecatid')
					->exists();

					$selectColumns = [
						'grievanceticketid',
						'tktno',
						'deptcode',
						DB::raw("to_char(createdon,'dd-MM-yyyy') as createdon")
					];

				if ($hasCategoryColumn) {
					$selectColumns[] = 'grievancecatid';
				}

				$data = DB::table('audit.grievanceticket')
					->select($selectColumns)
					->where('mobilenumber', $mobileNumber)
					->orderByDesc('grievanceticketid')
					->get();

				$data->transform(function ($row) use ($department, $category, $hasCategoryColumn) {
					$row->department = $department[$row->deptcode] ?? '-';
					$row->categoryname = $hasCategoryColumn ? ($category[$row->grievancecatid] ?? '-') : '-';
					$row->status_label = 'Registered';

					return $row;
				});
			} catch (\Exception $e) {
				$data = collect();
			}

			return response()->json([
				'status' => true,
				'data' => $data
			]);
		}

		private function camsgptFallbackQuestions()
		{
			return collect([
				[
					'id' => 1,
					'question' => 'What is CAMS?',
					'answer' => 'CAMS is the Comprehensive Audit Management System used to manage audit planning, scheduling, allocation, inspection, reporting, and support workflows.'
				],
				[
					'id' => 2,
					'question' => 'Who can use CAMS?',
					'answer' => 'CAMS provides role-based access for auditors, auditee institutions, department HOD users, DGA users, and support/grievance users.'
				],
				[
					'id' => 3,
					'question' => 'How can I register a grievance?',
					'answer' => 'Use the Register Grievance option on the homepage. Verify your mobile number with OTP, enter the required details, and submit the grievance.'
				],
					[
						'id' => 4,
						'question' => 'What are the main audit workflow stages?',
						'answer' => 'The main flow covers audit plan allocation, scheduling and intimation, work allocation, audit slip issue, auditee reply, inspection, and final report.'
					],
					[
						'id' => 5,
						'question' => 'Where can auditors start their CAMS work?',
						'answer' => 'Auditors can use the Auditor Login option to access planning, audit slips, inspection entries, report work, and related assigned tasks.'
					],
					[
						'id' => 6,
						'question' => 'How do auditee institutions reply in CAMS?',
						'answer' => 'Auditee institutions can log in through the Auditee Login option and submit replies, compliance details, and supporting information for audit observations.'
					],
					[
						'id' => 7,
						'question' => 'How can a user track support requests?',
						'answer' => 'A user can register a grievance from the homepage and then use the grievance status flow to follow the ticket progress.'
					],
					[
						'id' => 8,
						'question' => 'Why is CAMS useful for departments?',
						'answer' => 'CAMS keeps audit planning, allocation, communication, reporting, and compliance records in one digital workspace for easier monitoring and follow-up.'
					],
				]);
			}

		public function camsgptQuestions()
		{
			try {
				$questions = DB::table('audit.camsgpt_questions')
					->select('id', 'question')
					->where('statusflag', 'Y')
					->orderBy('display_order')
					->orderBy('id')
					->get();
			} catch (\Throwable $e) {
				$questions = $this->camsgptFallbackQuestions()
					->map(function ($item) {
						return [
							'id' => $item['id'],
							'question' => $item['question'],
						];
					})
					->values();
			}

			return response()->json([
				'status' => true,
				'questions' => $questions
			]);
		}

		public function camsgptAnswer($id)
		{
			try {
				$answer = DB::table('audit.camsgpt_questions')
					->select('answer')
					->where('id', (int) $id)
					->where('statusflag', 'Y')
					->first();

				if ($answer) {
					return response()->json([
						'status' => true,
						'answer' => $answer->answer
					]);
				}
			} catch (\Throwable $e) {
				$answer = $this->camsgptFallbackQuestions()
					->firstWhere('id', (int) $id);

				if ($answer) {
					return response()->json([
						'status' => true,
						'answer' => $answer['answer']
					]);
				}
			}

			return response()->json([
				'status' => false,
				'message' => 'CAMSGPT answer is not available.'
			], 404);
		}
	
	public function saveGrievance(Request $request)
	    {


        $departments = [
            'HRIA' => '01',
            'LFA'     => '02',
            'SGA'         => '03',
            'DCA'        => '04',
            'Milk'      => '05'
        ];
        // echo ($request->deptcode);
        $deptcode_valid = $departments[trim($request->deptcode)] ?? null;

        if ($deptcode_valid === null) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'deptcode' => ['Invalid Department selected.']
                ]
            ], 422);
        }

        $request->merge([
            'deptcode_valid' => $deptcode_valid
        ]);

        $validator = Validator::make($request->all(), [

            'name' => [
                'required',
                'max:70',
                'regex:/^[A-Za-z ]+$/'
            ],

            'email' => [
                'nullable',
                'max:30',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],

            'mobilenumber' => [
                'required',
                'digits:10',
                'regex:/^[6-9][0-9]{9}$/'
            ],

            'deptcode_valid' => [
                'required',
                Rule::in(array_values($departments))
            ],

            'category' => [
                'required',
                'integer',
                'min:1'
            ],
            'description' => [
                'required',
                'max:750',
                'not_regex:/(<\sscript\b|<\/\sscript\b|javascript:|\b(alert|prompt|confirm|eval)\s*\(|\b(select|insert|update|delete|drop|truncate|alter|union|exec|execute|create|grant|revoke)\b(?:\s|\()|--|\/\.?\*\/|\/\/|;)/i',
                'regex:/^[A-Za-z0-9\s\.,:;\'"\-\(\)\/&\r\n]*$/'
            ],

            'file' => [
                'nullable',
                'mimes:jpg,pdf',
                'max:200'
            ],


        ], [

            'name.required' => 'Enter Name',
            'name.regex' => 'Only letters and spaces allowed.',

            'email.regex' => 'Enter valid email.',

            'mobilenumber.required' => 'Enter Mobile Number',
            'mobilenumber.digits' => 'Mobile Number must be 10 digits.',
            'mobilenumber.regex' => 'Enter valid mobile number.',

            'deptcode.required' => 'Select Department.',
            'deptcode.in' => 'Invalid Department selected.',

            'description.required' => 'Enter Description.',
            'description.regex' => 'Special characters are not allowed.',

            'file.mimes' => 'Only JPG, JPEG, PNG and PDF files are allowed.',
            'file.max' => 'Maximum file size is 500 KB.',
            'category.required' => 'Select Category.',
            'category.in' => 'Invalid Category selected.',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (!session('grievance_mobile_verified') || session('grievance_mobile_number') !== $request->mobilenumber) {
            return response()->json([
                'status' => false,
                'message' => 'Please verify the mobile number before submitting grievance.'
            ], 422);
        }

        DB::beginTransaction();

        try {

            $fileuploadId = null;

            if ($request->hasFile('file')) {

                $file = $request->file('file');
                $destinationPath = 'uploads/grievance/';

                $destinationarray = [
                    $request->deptcode,
                    'grievance',
                ];

                $uploadResult = $this->fileUploadService->uploadFile(
                    $file,
                    $destinationPath,
                    $request->uploadid ?? '',
                    $destinationarray
                );

                $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
            }

            // Get Department Details
            $dept = DB::table('audit.mst_dept')
                ->where('deptcode', $request->deptcode_valid)
                ->first();

            if (!$dept) {
                throw new \Exception('Department not found.');
            }

            // H, L, S, D, M
            $deptPrefix = strtoupper(substr(trim($dept->deptesname), 0, 1));

            // G26LA
            $ticketPrefix = View::shared('grievanceLetter') . date('y') . $deptPrefix . View::shared('AuditLetter');

            // Get Last Ticket No for same department
            $lastTicket = DB::table('audit.grievanceticket')
                ->where('deptcode', $request->deptcode_valid)
                ->where('tktno', 'like', $ticketPrefix . '%')
                ->lockForUpdate()
                ->orderByDesc('grievanceticketid')
                ->value('tktno');

            if ($lastTicket) {

                $lastRunningNo = (int) substr($lastTicket, -6);
                $nextRunningNo = $lastRunningNo + 1;
            } else {

                $nextRunningNo = 1;
            }

            $ticketNo = $ticketPrefix .
                str_pad($nextRunningNo, 6, '0', STR_PAD_LEFT);

            $data = [
                'tktno'       => $ticketNo,
                'username'       => $request->name,
                'email'          => $request->email,
                'mobilenumber'   => $request->mobilenumber,
                'deptcode'       => $request->deptcode_valid,
                'description'    => $request->description,
                'grievancecatid' => $request->category,
                'createdon'      => View::shared('get_nowtime'),

                'ipaddress'      => $request->ip(),
                'machinename'    => gethostbyaddr($request->ip()),
                'browsername'    => substr($request->userAgent(), 0, 200)
            ];



            if ($fileuploadId) {
                $data['fileuploadid'] = $fileuploadId;
            }

            $grievanceId = DB::table('audit.grievanceticket')
                ->insertGetId($data, 'grievanceticketid');

            DB::commit();

            return response()->json([
                'status'      => true,
                'ticketno'    => $ticketNo,
                'grievanceid' => $grievanceId,
                'message'     => 'Ticket No: ' . $ticketNo
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
	// public function saveGrievance(Request $request)
	// {


	// 		$validator = Validator::make($request->all(), [

	// 		'name' => [
	// 			'required',
	// 			'max:70',
	// 			'regex:/^[A-Za-z ]+$/'
	// 		],

	// 		'email' => [
	// 			'nullable',
	// 			'max:30',
	// 			'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
	// 		],

	// 		'mobilenumber' => [
	// 			'required',
	// 			'digits:10',
	// 			'regex:/^[6-9][0-9]{9}$/'
	// 		],

	// 			'deptcode' => [
	// 				'required',
	// 				Rule::exists('audit.mst_dept', 'deptcode')
	// 					->where(function ($query) {
	// 						return $query->where('statusflag', 'Y');
	// 					})
	// 			],

	// 		'category' => [
	// 			'required',
	// 			'integer',
	// 			Rule::exists('audit.mst_grievancecategory', 'grievancecatid')
	// 				->where(function ($query) {
	// 					return $query->where('statusflag', 'Y');
	// 				})
	// 		],
	// 		'description' => [
	// 			'required',
	// 			'max:750',
	// 			'not_regex:/(<\s*script\b|<\/\s*script\b|javascript:|\b(alert|prompt|confirm|eval)\s*\(|\b(select|insert|update|delete|drop|truncate|alter|union|exec|execute|create|grant|revoke)\b(?:\s|\()|--|\/\*.*?\*\/|\/\/|;)/i',
	// 			'regex:/^[A-Za-z0-9\s\.,:;\'"\-\(\)\/&\r\n]*$/'
	// 		],

	// 		'file' => [
	// 			'nullable',
	// 			'mimes:jpg,pdf',
	// 			'max:200'
	// 		],


	// 	], [

	// 		'name.required' => 'Enter Name',
	// 		'name.regex' => 'Only letters and spaces allowed.',

	// 		'email.regex' => 'Enter valid email.',

	// 		'mobilenumber.required' => 'Enter Mobile Number',
	// 		'mobilenumber.digits' => 'Mobile Number must be 10 digits.',
	// 		'mobilenumber.regex' => 'Enter valid mobile number.',

	// 		'deptcode.required' => 'Select Department.',
	// 		'deptcode.in' => 'Invalid Department selected.',

	// 		'description.required' => 'Enter Description.',
	// 		'description.regex' => 'Special characters are not allowed.',

	// 		'file.mimes' => 'Only JPG, JPEG, PNG and PDF files are allowed.',
	// 		'file.max' => 'Maximum file size is 500 KB.',
	// 		'category.required' => 'Select Category.',
	// 		'category.in' => 'Invalid Category selected.',
	// 	]);

	// 	if ($validator->fails()) {

	// 		return response()->json([
	// 			'status' => false,
	// 			'errors' => $validator->errors()
	// 		], 422);
	// 	}

	// 	if (!session('grievance_mobile_verified') || session('grievance_mobile_number') !== $request->mobilenumber) {
	// 		return response()->json([
	// 			'status' => false,
	// 			'message' => 'Please verify the mobile number before submitting grievance.'
	// 		], 422);
	// 	}

	// 	DB::beginTransaction();

	// 	try {



	// 		$fileUploadId = null;

	// 		if ($request->hasFile('file')) {

	// 			$file = $request->file('file');
	// 			$destinationPath = 'uploads/grievance/';

	// 			$destinationarray = [
	// 				$request->deptcode,
	// 				'grievance',
	// 			];

	// 			$uploadResult = $this->fileUploadService->uploadFile(
	// 				$file,
	// 				$destinationPath,
	// 				$request->uploadid ?? '',
	// 				$destinationarray
	// 			);


	// 			$fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
	// 		}

	// 		$data = [
	// 			'username' => $request->name,
	// 			'email' => $request->email,
	// 				'mobilenumber' => $request->mobilenumber,
	// 				'deptcode' => $request->deptcode,
	// 			'description' => $request->description,
	// 			'grievancecatid'      => $request->category,
	// 			'createdon' => View::shared('get_nowtime'),
	// 			'ipaddress'       => $request->ip(),
	// 			'machinename' => gethostbyaddr($request->ip()),
	// 			'browsername'     => $request->userAgent()


	// 		];

	// 		if ($request->hasFile('file')) {

	// 			$data['fileuploadid'] = $fileuploadId;
	// 		}
	// 		DB::table('audit.grievanceticket')->insert(
	// 			$data
	// 		);


	// 		DB::commit();

	// 		session()->forget(['grievance_mobile_verified', 'grievance_mobile_number']);


	// 		return response()->json([
	// 			'status' => true,
	// 			'message' => 'Grievance submitted successfully.'
	// 		]);
	// 	} catch (\Exception $e) {

	// 		DB::rollBack();

	// 		return response()->json([
	// 			'status' => false,
	// 			'message' => $e->getMessage()
	// 		], 500);
	// 	}
	// }
}
