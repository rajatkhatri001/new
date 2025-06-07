<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 18 Dec 2018 09:12:08 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class SiteSetting
 * 
 * @property int $id
 * @property string $key
 * @property string $value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class SiteSetting extends Eloquent
{
	protected $table = 'site_setting';

	protected $fillable = [
		'key',
		'value',
		'title',
		'description'
	];
}
