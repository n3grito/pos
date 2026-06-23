<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    protected $fillable = ['mailer', 'host', 'port', 'username', 'encrypted_password', 'encryption', 'from_address', 'from_name'];

    protected function casts(): array
    {
        return [
            'port' => 'integer',
        ];
    }
}
