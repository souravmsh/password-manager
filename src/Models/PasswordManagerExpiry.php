<?php

namespace Souravmsh\PasswordManager\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordManagerExpiry extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $guarded = ['id'];
    protected $table = 'password_manager_expiry';

    public function user()
    {
    	return $this->belongsTo((new (config('password-manager.user_model')))::class, 'user_id', 'id');
    }
}
