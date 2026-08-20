<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Services\PHPMailerService;
use App\Services\SmsService;

class CronController extends Controller
{
	protected $smsService;
	protected $mailService;

	// Constructor Injection (NO parent::__construct())
	public function __construct(PHPMailerService $mailService, SmsService $smsService)
	{
		$this->mailService = $mailService;
		$this->smsService  = $smsService;
	}

	/**
	 * Validate secret key
	 */
	private function validateSecret(Request $request, ?string $secretFromRoute = null)
	{
		$secretHeader = $request->header('X-Cron-Secret');
		$secret = $secretHeader ?: $secretFromRoute;

		if (!$secret || $secret !== env('CRON_SECRET_KEY')) {
			Log::warning('Unauthorized cron attempt', [
				'ip'   => $request->ip(),
				'path' => $request->path(),
			]);
			abort(403, 'Unauthorized Access');
		}
	}

	/**
	 * Optional IP Whitelist
	 */
	private function isIpAllowed(Request $request)
	{
		$allowed = [
			// Example: '127.0.0.1'
		];

		if (empty($allowed)) {
			return true;
		}

		return in_array($request->ip(), $allowed, true);
	}

	/**
	 * Cron Job 1
	 */
	public function cronJob1(Request $request, ?string $secret = null)
	{
		$this->validateSecret($request, $secret);

		if (!$this->isIpAllowed($request)) {
			abort(403, 'IP not allowed');
		}

		Log::info("CronJob1 started");

		$mailCount = 0;

		try {
			// 1. Call PostgreSQL function
			$records = DB::select("SELECT * FROM audit.daily_auditeeactivity_maildel() AS result");

			if (empty($records)) {
				Log::warning("No rows returned by function.");
				return response()->json(['status' => true, 'message' => 'No data'], 200);
			}

			$jsonData = $records[0]->result ?? null;
			if (!$jsonData) {
				Log::warning("Empty JSON result.");
				return response()->json(['status' => true, 'message' => 'Empty JSON'], 200);
			}

			$data = json_decode($jsonData, true);

			if (json_last_error() !== JSON_ERROR_NONE) {
				throw new \Exception("Invalid JSON returned from PostgreSQL function");
			}

			// 2. Loop records
			foreach ($data as $rec) {
				if (empty($rec['email'])) continue;

				$htmlTemplate = file_get_contents(resource_path('views/Email/InstitutionSummary.html'));

				$exitmeetlabel = $rec['exitmeetdateisdone']
					? 'Exitmeeting Date'
					: 'Proposed Exitmeeting Date';

				$dynamicData = [
					'{{heading}}'          => 'Daily Audit Summary',
					'{{instename}}'        => $rec['instename'],
					'{{todaypending}}'     => $rec['todaypending'],
					'{{totalpending}}'     => $rec['totalpending'],
					'{{entrymeetdate}}'    => date('d-m-Y', strtotime($rec['entrymeetdate'])),
					'{{exitmeetdate}}'     => date('d-m-Y', strtotime($rec['exitmeetdate'])),
					'{{yearcode_mapping}}' => $rec['yearcode_mapping'],
					'{{exitmeetlabel}}'    => $exitmeetlabel,
					'{{today}}'            => date('d-m-Y', strtotime(View::shared('get_nowtime'))),
					'{{senton}}'           => date('d-m-Y h:i A', strtotime(View::shared('get_nowtime'))),
				];

				$html = str_replace(array_keys($dynamicData), array_values($dynamicData), $htmlTemplate);

				// Send email
				$email = $rec['email'];
				$subject = "Audit Slip Status - CAMS";

				$status = $this->mailService->sendEmail($email, $subject, $html, '');

				if ($status) {
					$mailCount++;
					Log::info("Mail sent ? $email");
				} else {
					Log::warning("Failed to send mail ? $email");
				}
			}

			// Insert into history table
			DB::table('audit.history_cronjob')->insert([
				'processdate' => View::shared('get_nowtime'),
				'process'     => 'Auditee Daily Summary Mail',
				'details'     => json_encode(['mailcount' => $mailCount]),
				'statusflag'  => 'S',
			]);

			return response()->json([
				'status'  => true,
				'message' => "CronJob1 complete",
				'sent'    => $mailCount,
			]);
		} catch (\Exception $e) {
			Log::error("CronJob1 failed: " . $e->getMessage());

			DB::table('audit.history_cronjob')->insert([
				'processdate' => View::shared('get_nowtime'),
				'process'     => 'Auditee Daily Summary Mail',
				'details'     => '',
				'statusflag'  => 'F',
			]);

			return response()->json([
				'status'  => false,
				'message' => "CronJob1 failed",
				'error'   => $e->getMessage(),
			], 500);
		}
	}

	/**
	 * Cron Job 2
	 */
	public function cronJob2(Request $request, ?string $secret = null)
	{
		$this->validateSecret($request, $secret);

		if (!$this->isIpAllowed($request)) {
			abort(403, 'IP not allowed');
		}

		Log::info("CronJob2 executed");

		return response()->json(['status' => true, 'message' => 'CronJob2 executed']);
	}

	/**
	 * Run both cron jobs
	 */
	public function runAll(Request $request, ?string $secret = null)
	{
		$this->validateSecret($request, $secret);

		if (!$this->isIpAllowed($request)) {
			abort(403, 'IP not allowed');
		}

		$job1 = $this->cronJob1($request, $secret)->getData(true);
		$job2 = $this->cronJob2($request, $secret)->getData(true);

		return response()->json([
			'status' => true,
			'message' => "All cron jobs executed",
			'job1' => $job1,
			'job2' => $job2,
		]);
	}
}
