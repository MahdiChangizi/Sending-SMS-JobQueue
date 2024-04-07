<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotifyCode extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'user_id', 'expired_at'];
    public $timestamps = false;

    protected int $code;

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    # Create a code for user
    public function scopeGenerateCode($query, User $user) : int
    {
        # If the code has not expired
        if ($get_code = $this->getAliveCodeForUser(user: $user)) {
            $this->code = $get_code->code;
        } else {
            do {$this->code = mt_rand(100000, 999999);} # create code random
            while($this->checkCodeisUniqe(code: $this->code, user: $user)); # uniqe code

            $user->codes()->delete(); # delete all codes expired user

            # Create new Code to DB
            $user->codes()->create([
                'code'       => $this->code,
                'expired_at' => now()->addMinutes(2)
            ]);
        }

        # Return generated code
        return $this->code;
    }

    # Checking the code whether it is unique or not
    protected function checkCodeisUniqe(int $code, User $user) : bool
    {
        return !! $user->codes->where("code", $code)->first();
    }

    # Returning a code that has not expired
    protected function getAliveCodeForUser(User $user) : NotifyCode
    {
        return $user->codes->where("expired_at", '>', now())->first();
    }

}
