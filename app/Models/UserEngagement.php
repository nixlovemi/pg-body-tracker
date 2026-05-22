<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Keeps engagement delivery history and user preferences.
 *
 * Notes about dispatch tracking fields:
 * - `last_sent_type` is a lightweight classifier for the latest engagement dispatch
 *   (for example: `digest`). It exists mostly for auditability and future segmentation.
 * - `last_payload` stores the last generated digest payload for inspection/debug.
 * - `trigger_state` stores per-alert-type timestamps used by cooldown rules.
 */
class UserEngagement extends Model
{
    use HasFactory;

    public const ALERT_INACTIVE_LOGIN = 'inactive_login';
    public const ALERT_MISSING_SETUP = 'missing_setup';
    public const ALERT_BIRTHDAY_TODAY = 'birthday_today';
    public const ALERT_GOAL_NEAR_DEADLINE = 'goal_near_deadline';
    public const ALERT_CLIENT_WITHOUT_RECENT_AVALIATION = 'client_without_recent_avaliation';
    public const ALERT_REVALUATION_NEAR = 'revaluation_near';

    public const SENT_TYPE_DIGEST = 'digest';

    protected $fillable = [
        'user_id',
        'last_sent_at',
        'last_sent_type',
        'last_payload',
        'trigger_state',
        'alert_preferences',
        'opt_out',
    ];

    protected $casts = [
        'last_sent_at' => 'datetime',
        'last_payload' => 'array',
        'trigger_state' => 'array',
        'alert_preferences' => 'array',
        'opt_out' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function getDefaultAlertPreferences(): array
    {
        return [
            self::ALERT_INACTIVE_LOGIN => true,
            self::ALERT_MISSING_SETUP => true,
            self::ALERT_BIRTHDAY_TODAY => true,
            self::ALERT_GOAL_NEAR_DEADLINE => true,
            self::ALERT_CLIENT_WITHOUT_RECENT_AVALIATION => true,
            self::ALERT_REVALUATION_NEAR => true,
        ];
    }

    public function getMergedAlertPreferences(): array
    {
        return array_merge(
            self::getDefaultAlertPreferences(),
            $this->alert_preferences ?? []
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
