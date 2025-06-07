<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 25 Dec 2018 12:25:08 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class SiteSetting
 * 
 * @property int $id
 * @property string $key
 * @property text $value
 * @property string $title
 * @property text $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class EmailSetting extends Eloquent
{

	protected $table = 'email_setting';

	protected $fillable = [
		'key',
		'value',
		'description',
		'title'
	];
}
