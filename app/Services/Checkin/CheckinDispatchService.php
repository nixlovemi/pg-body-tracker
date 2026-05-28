<?php

namespace App\Services\Checkin;

use App\Helpers\LocalLogger;
use App\Helpers\SysUtils;
use App\Mail\SendCheckinFollowupLink;
use App\Models\CheckinConfig;
use App\Models\UrlShort;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class CheckinDispatchService
{
    private const MODE_SCHEDULED = 'scheduled';
    private const MODE_REMINDER = 'reminder';

    public function dispatchDue(?int $nutritionistUserId = null, bool $dryRun = false): array
    {
        $nowTz = now()->setTimezone(env('APP_TIME_ZONE'));
        $todayYmd = $nowTz->format('Y-m-d');

        $query = CheckinConfig::query()
            ->with(['client.user'])
            ->where('active', true)
            ->whereNotNull('next_checkin_date');

        if ($nutritionistUserId) {
            $query->whereHas('client', function ($q) use ($nutritionistUserId) {
                $q->where('user_id', $nutritionistUserId);
            });
        }

        $totals = [
            'configs_scanned' => 0,
            'eligible' => 0,
            'links_dispatched' => 0,
            'would_dispatch' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        foreach ($query->get() as $config) {
            $totals['configs_scanned']++;

            $dispatchMode = $this->resolveDispatchMode($config, $nowTz, $todayYmd);
            if ($dispatchMode === null) {
                $totals['skipped']++;
                continue;
            }

            if (!$this->canDispatchForConfig($config, $todayYmd)) {
                $totals['skipped']++;
                continue;
            }

            $totals['eligible']++;
            $totals['would_dispatch']++;

            if ($dryRun) {
                continue;
            }

            try {
                $this->dispatchForConfig($config, $todayYmd, $dispatchMode, $nowTz);
                $totals['links_dispatched']++;
            } catch (\Throwable $th) {
                $totals['errors']++;
                $totals['skipped']++;
                LocalLogger::log('checkin_dispatch_due_error', [
                    'checkin_config_id' => $config->id,
                    'client_id' => $config->client_id,
                    'message' => $th->getMessage(),
                ]);
            }
        }

        return $totals;
    }

    private function canDispatchForConfig(CheckinConfig $config, string $todayYmd): bool
    {
        $client = $config->client;
        $nutritionist = $client?->user;

        if (!$client || !$nutritionist) {
            return false;
        }

        if (!$nutritionist->isManager() && !$nutritionist->isRoot()) {
            return false;
        }

        if (!$nutritionist->hasPremiumPlan()) {
            return false;
        }

        if (!filter_var($client->email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $lastSentYmd = $config->last_checkin_sent_date?->format('Y-m-d');
        if ($lastSentYmd === $todayYmd) {
            return false;
        }

        return true;
    }

    private function resolveDispatchMode(CheckinConfig $config, Carbon $nowTz, string $todayYmd): ?string
    {
        $hasPendingUnanswered = $this->hasPendingUnansweredCycle($config);

        if ($hasPendingUnanswered && $this->isReminderDue($config, $nowTz)) {
            return self::MODE_REMINDER;
        }

        $isScheduledDue = optional($config->next_checkin_date)?->format('Y-m-d') <= $todayYmd;
        if ($isScheduledDue && !$hasPendingUnanswered) {
            return self::MODE_SCHEDULED;
        }

        return null;
    }

    private function hasPendingUnansweredCycle(CheckinConfig $config): bool
    {
        $sentDateYmd = $this->getLastSentDateYmd($config);
        if ($sentDateYmd === null) {
            return false;
        }

        $lastAnsweredYmd = optional($config->last_checkin_date)?->format('Y-m-d');
        return $lastAnsweredYmd === null || $lastAnsweredYmd < $sentDateYmd;
    }

    private function isReminderDue(CheckinConfig $config, Carbon $nowTz): bool
    {
        $maxReminders = max(0, (int) env('CHECKIN_MAX_REMINDERS_PER_CYCLE', 1));
        $alreadySent = (int) ($config->unanswered_reminders_sent ?? 0);
        if ($alreadySent >= $maxReminders) {
            return false;
        }

        $lastSentAt = $this->getLastSentAt($config);
        if ($lastSentAt === null) {
            return false;
        }

        $resendAfterHours = max(1, (int) $config->link_expires_hours);
        return $nowTz->greaterThanOrEqualTo($lastSentAt->copy()->addHours($resendAfterHours));
    }

    private function getLastSentAt(CheckinConfig $config): ?Carbon
    {
        if ($config->last_checkin_sent_at) {
            return Carbon::parse($config->last_checkin_sent_at)->setTimezone(env('APP_TIME_ZONE'));
        }

        if ($config->last_checkin_sent_date) {
            return Carbon::parse($config->last_checkin_sent_date)->setTimezone(env('APP_TIME_ZONE'))->startOfDay();
        }

        return null;
    }

    private function getLastSentDateYmd(CheckinConfig $config): ?string
    {
        $lastSentAt = $this->getLastSentAt($config);
        return $lastSentAt?->format('Y-m-d');
    }

    private function dispatchForConfig(CheckinConfig $config, string $todayYmd, string $dispatchMode, Carbon $nowTz): void
    {
        $client = $config->client;
        $nutritionistUserId = (int) $client->user_id;

        $portalLink = $this->buildSignedFollowupLink($config);
        $shortUrl = UrlShort::make($portalLink);

        Mail::to($client->email)
            ->send(new SendCheckinFollowupLink($client, $shortUrl));

        $this->executeAsNutritionistOwner($nutritionistUserId, function () use ($config, $todayYmd, $dispatchMode, $nowTz) {
            $config->last_checkin_sent_date = $todayYmd;
            $config->last_checkin_sent_at = $nowTz;

            if ($dispatchMode === self::MODE_REMINDER) {
                $config->unanswered_reminders_sent = ((int) $config->unanswered_reminders_sent) + 1;
            } else {
                $config->unanswered_reminders_sent = 0;
                $config->next_checkin_date = SysUtils::applyTimezone($todayYmd)
                    ->addDays((int) $config->interval_days)
                    ->format('Y-m-d');
            }

            $config->save();
        });
    }

    private function buildSignedFollowupLink(CheckinConfig $config): string
    {
        return URL::temporarySignedRoute(
            'app.checkin.followup.form',
            now()->addHours((int) $config->link_expires_hours),
            ['configCodedId' => $config->codedId]
        );
    }

    private function executeAsNutritionistOwner(int $ownerUserId, callable $callback): void
    {
        $currentUser = SysUtils::getLoggedInUser();
        $switched = false;

        try {
            if (!$currentUser || $currentUser->id !== $ownerUserId) {
                if (!SysUtils::loginUserTempById($ownerUserId, 5)) {
                    throw new \RuntimeException('Unable to impersonate nutritionist owner for check-in dispatch.');
                }
                $switched = true;
            }

            $callback();
        } finally {
            if ($switched) {
                if ($currentUser) {
                    SysUtils::loginUser($currentUser);
                } else {
                    SysUtils::logout(false);
                }
            }
        }
    }
}
