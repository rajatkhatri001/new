<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class EmailTemplate extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'email_template';

	protected $casts = [
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'title',
		'slug',
		'from_name',
		'from_email',
		'subject',
		'content',
		'created_by',
		'updated_by'
	];
	public function admin()
	{
		return $this->belongsTo(\App\Models\Admin::class, 'updated_by');
	}
}
