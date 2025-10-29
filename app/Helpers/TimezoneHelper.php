<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimezoneHelper
{
    /**
     * Convert UTC timestamp to user's timezone
     */
    public static function toUserTimezone($utcTimestamp, $userTimezone = null): Carbon
    {
        $userTimezone = $userTimezone ?: session('timezone', 'Asia/Jakarta');
        
        // Ensure we start with UTC
        $carbon = Carbon::parse($utcTimestamp)->utc();
        
        // Convert to user's timezone
        return $carbon->setTimezone($userTimezone);
    }
    
    /**
     * Convert user's timezone timestamp to UTC for database storage
     */
    public static function toUtc($timestamp, $userTimezone = null): Carbon
    {
        $userTimezone = $userTimezone ?: session('timezone', 'Asia/Jakarta');
        
        // Parse timestamp in user's timezone
        $carbon = Carbon::parse($timestamp, $userTimezone);
        
        // Convert to UTC for database storage
        return $carbon->utc();
    }
    
    /**
     * Get current timestamp in UTC (for database storage)
     */
    public static function nowUtc(): Carbon
    {
        return Carbon::now('UTC');
    }
    
    /**
     * Get current timestamp in user's timezone (for display)
     */
    public static function nowUser($userTimezone = null): Carbon
    {
        $userTimezone = $userTimezone ?: session('timezone', 'Asia/Jakarta');
        return Carbon::now($userTimezone);
    }
    
    /**
     * Format timestamp for display in user's timezone
     */
    public static function formatForDisplay($utcTimestamp, $format = 'd M Y H:i', $userTimezone = null): string
    {
        return self::toUserTimezone($utcTimestamp, $userTimezone)->format($format);
    }
    
    /**
     * Get date string in user's timezone
     */
    public static function getDateString($utcTimestamp, $userTimezone = null): string
    {
        return self::toUserTimezone($utcTimestamp, $userTimezone)->toDateString();
    }
}
