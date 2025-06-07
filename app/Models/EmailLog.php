<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
use App\Models\EmailTemplate;
use Mail;
use Cache;

class EmailLog extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'email_log';
	public $timestamps = true;
	protected $casts = [
		'is_sent' => 'int',
		'is_read' => 'int',
	];
	protected $dates = [
		'sent_on',
		'read_on',
	];
	protected $fillable = [
		'from_email',
		'from_name',
		'to_email',
		'to_name',
		'subject',
		'content',
		'is_sent',
		'sent_on',
		'is_read',
		'read_on',
	];
	public static function generateLog($templateSlug, $userId, $isAdmin = false, $emailData=[])
	{
		$emailTemplate = EmailTemplate::where([['slug', '=', $templateSlug]])->first();
		if ($emailTemplate == null) {
			return false;
		}
		if ($isAdmin == true) {
			$user = Admin::where([['id', '=', $userId]])->first();
			if ($user == null) {
					return false;
			}
			$name = $user->first_name;
			$email = $user->email;
			// $token = $user->remember_token;
		} else {
			$name = $emailData['name'];
			$email = $emailData['email'];
		}
		if(isset($emailData['invite_msg'])){
			$emailBody = $emailData['invite_msg'];
		}else{
			$emailBody = $emailTemplate->content;
		}
		$siteName = Cache::get('site_name');
		switch ($templateSlug) {
			case 'Admin_Reset_Password':
				$user_name = $user->first_name;
				$token = $user->remember_token;
				$resent_link = url('admin/reset-password') . '/' . $token;
				$emailBody = str_replace('{{USER_NAME}}', $user_name, $emailBody);
				$emailBody = str_replace('{{RESET_LINK}}', $resent_link, $emailBody);
				$emailBody = str_replace('{{SITE_NAME}}', $siteName, $emailBody);
			break;
		}
		$emailLog = self::create([
			"from_name" => $emailTemplate->from_name,
			"from_email" => $emailTemplate->from_email,
			"to_name" => $name,
			"to_email" => $email,
			"subject" => $emailTemplate->subject,
			"content" => $emailBody,
			"created_at" => date("Y-m-d H:i:s"),
			"is_sent" => 1,
			"sent_on" => date("Y-m-d H:i:s"),
		]);
		try {
			Mail::send([], [], function ($message) use ($emailLog) {
				$message->to($emailLog->to_email, $emailLog->to_name)
						->subject($emailLog->subject)
						->setBody($emailLog->content, 'text/html');
				$message->from($emailLog->from_email, $emailLog->from_name);
			});
		} catch (\Exception $ex) {
			// \Log::info("Mail Error:".$ex->getMessage());
			// echo "<pre>";
			// print_r($ex->getMessage());
			// exit();
		}
		if (!$emailLog) {
			return false;
		}
		return true;
	}
}