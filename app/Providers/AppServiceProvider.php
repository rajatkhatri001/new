<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Hivokas\LaravelPassportSocialGrant\Resolvers\SocialUserResolverInterface;
use App\Models\EmailSetting;
use App\Models\Event;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;
use Config;
use Cache;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // $sitesetting = SiteSetting::all();
		// foreach ($sitesetting as $sitesettings) {
		// 	Cache::forever($sitesettings->key, $sitesettings->value);
		// }

		// $emailSettingObj = EmailSetting::all();
		// foreach ($emailSettingObj as $setting) {
		// 	Cache::forever($setting->key, $setting->value);
		// }
		// $config = array(
		// 	'driver' => Cache::get('MAIL_DRIVER'),
		// 	'host' => Cache::get('MAIL_HOST'),
		// 	'port' => Cache::get('MAIL_PORT'),
		// 	'from' => array('address' => Cache::get('MAIL_USERNAME'), 'name' => Cache::get('MAIL_FROM_NAME')),
		// 	'encryption' => Cache::get('MAIL_ENCRYPTION'),
		// 	'username' => Cache::get('MAIL_USERNAME'),
		// 	'password' => Cache::get('MAIL_PASSWORD'),
		// 	'sendmail' => '/usr/sbin/sendmail -bs',
		// 	'pretend' => false,
		// );
		// Config::set('mail', $config);
		// Schema::defaultStringLength(191);
    }
}
