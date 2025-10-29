<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class BaseModel extends Model
{
    protected function asDateTime($value): Carbon
    {
        $date = parent::asDateTime($value);

        // Pick timezone: user's DB timezone if logged in, otherwise session, fallback app timezone
        if (Auth::check()) {
            $timezone = Auth::user()->timezone ?? config('app.timezone');
        } else {
            $timezone = Session::get('timezone', config('app.timezone'));
        }

        return $date->clone()->setTimezone($timezone);
    }

    // Optional: define how dates look when converted to JSON/array
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
