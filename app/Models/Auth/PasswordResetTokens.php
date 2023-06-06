<?php
declare(strict_types=1);

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class PasswordResetTokens extends Model
{
    protected $table = 'password_reset_tokens';
}
