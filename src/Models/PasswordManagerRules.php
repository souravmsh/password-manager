<?php

namespace Souravmsh\PasswordManager\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordManagerRules extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $guarded = ['id'];
    protected $table = 'password_manager_rules';
}
