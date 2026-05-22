<?php

namespace App\Services\Engagement;

use App\Jobs\SendEngagementDigestEmailJob;
use App\Models\Avaliation;
use App\Models\Client;
use App\Models\User;
use App\Models\UserEngagement;
use Carbon\Carbon;

class EngagementDigestService
{
    public function dispatchDueUsers(?int $forcedUserId = null, bool $dryRun = false): array
    {
        $usersQuery = User::query()
            ->where('active', true)
            ->whereIn('role', [User::ROLE_MANAGER, User::ROLE_ROOT]);

        if ($forcedUserId) {
            $usersQuery->where('id', $forcedUserId);
        }

        $totals = [
            'users_scanned' => 0,
            'emails_dispatched' => 0,
            'would_dispatch' => 0,
            'users_skipped' => 0,
        ];

        foreach ($usersQuery->get() as $user) {
            $totals['users_scanned']++;

            $payload = $this->buildDigestPayload($user);
            if (empty($payload['reasons'])) {
                $totals['users_skipped']++;
                continue;
            }

            $engagement = UserEngagement::query()->where('user_id', $user->id)->first();
            if (!$engagement && !$dryRun) {
                $engagement = UserEngagement::create(['user_id' => $user->id]);
            }
            if (!$engagement) {
                $engagement = new UserEngagement(['user_id' => $user->id]);
            }

            if (!$this->canSendNow($engagement, $payload)) {
                $totals['users_skipped']++;
                continue;
            }

            $totals['would_dispatch']++;

            if ($dryRun) {
                continue;
            }

            SendEngagementDigestEmailJob::dispatch($user->id, $payload)
                ->onQueue('emails');

            $this->markDispatched($engagement, $payload);
            $totals['emails_dispatched']++;
        }

        return $totals;
    }

    public function buildDigestPayload(User $user): array
    {
        $preferences = $this->getAlertPreferences($user);
        $abVariant = $this->resolveAbVariant($user);

        $payload = [
            'reasons' => [],
            'meta' => [
                'client_count' => $user->clients()->count(),
                'avaliation_count' => $user->avaliations()->count(),
                'ab_variant' => $abVariant,
            ],
        ];

        $reasons = [
            $this->buildInactiveLoginReason($user, $preferences),
            $this->buildMissingSetupReason($payload, $preferences),
            $this->buildBirthdayTodayReason($user, $preferences),
            $this->buildGoalNearDeadlineReason($user, $preferences),
            $this->buildClientWithoutRecentAvaliationReason($user, $preferences),
            $this->buildRevaluationNearReason($user, $preferences),
        ];

        $payload['reasons'] = array_values(array_filter($reasons));

        return $payload;
    }

    private function resolveAbVariant(User $user): string
    {
        if (!(bool) env('ENGAGEMENT_AB_TEST_ENABLED', true)) {
            return 'a';
        }

        $variantBPercent = max(0, min(100, (int) env('ENGAGEMENT_AB_TEST_B_PERCENT', 50)));
        $bucket = (abs(crc32('engagement_ab_' . $user->id)) % 100) + 1;

        return $bucket <= $variantBPercent ? 'b' : 'a';
    }

    private function buildInactiveLoginReason(User $user, array $preferences): ?array
    {
        $inactiveDaysThreshold = (int) env('ENGAGEMENT_INACTIVE_DAYS', 5);
        $lastLoginAt = $user->last_login_at ?? $user->created_at;
        if (!$lastLoginAt) {
            return null;
        }

        $daysWithoutLogin = Carbon::parse($lastLoginAt)->diffInDays(now());
        if (!$preferences[UserEngagement::ALERT_INACTIVE_LOGIN] || $daysWithoutLogin < $inactiveDaysThreshold) {
            return null;
        }

        return [
            'type' => UserEngagement::ALERT_INACTIVE_LOGIN,
            'days' => $daysWithoutLogin,
        ];
    }

    private function buildMissingSetupReason(array $payload, array $preferences): ?array
    {
        $missingClient = $payload['meta']['client_count'] === 0;
        $missingAvaliation = $payload['meta']['avaliation_count'] === 0;

        if (!$preferences[UserEngagement::ALERT_MISSING_SETUP] || (!$missingClient && !$missingAvaliation)) {
            return null;
        }

        return [
            'type' => UserEngagement::ALERT_MISSING_SETUP,
            'missing_client' => $missingClient,
            'missing_avaliation' => $missingAvaliation,
        ];
    }

    private function buildBirthdayTodayReason(User $user, array $preferences): ?array
    {
        $today = now()->format('m-d');
        $birthdaysToday = $user->clients()
            ->whereRaw("DATE_FORMAT(birthdate, '%m-%d') = ?", [$today])
            ->count();

        if (!$preferences[UserEngagement::ALERT_BIRTHDAY_TODAY] || $birthdaysToday <= 0) {
            return null;
        }

        return [
            'type' => UserEngagement::ALERT_BIRTHDAY_TODAY,
            'count' => $birthdaysToday,
        ];
    }

    private function buildGoalNearDeadlineReason(User $user, array $preferences): ?array
    {
        $goalsDays = (int) env('ENGAGEMENT_GOAL_NEAR_DAYS', 7);
        $goalsNear = Client::query()
            ->where('user_id', $user->id)
            ->whereHas('goals', function ($query) use ($goalsDays) {
                $query->whereBetween('deadline', [now()->startOfDay(), now()->addDays($goalsDays)->endOfDay()]);
            })
            ->count();

        if (!$preferences[UserEngagement::ALERT_GOAL_NEAR_DEADLINE] || $goalsNear <= 0) {
            return null;
        }

        return [
            'type' => UserEngagement::ALERT_GOAL_NEAR_DEADLINE,
            'count' => $goalsNear,
            'days' => $goalsDays,
        ];
    }

    private function buildClientWithoutRecentAvaliationReason(User $user, array $preferences): ?array
    {
        $noAvalDays = (int) env('ENGAGEMENT_NO_AVALIATION_DAYS', 30);
        $clientsNoRecentAval = Client::query()
            ->where('user_id', $user->id)
            ->whereDoesntHave('avaliations', function ($query) use ($noAvalDays) {
                $query->where('date', '>=', now()->subDays($noAvalDays)->format('Y-m-d'));
            })
            ->count();

        if (!$preferences[UserEngagement::ALERT_CLIENT_WITHOUT_RECENT_AVALIATION] || $clientsNoRecentAval <= 0) {
            return null;
        }

        return [
            'type' => UserEngagement::ALERT_CLIENT_WITHOUT_RECENT_AVALIATION,
            'count' => $clientsNoRecentAval,
            'days' => $noAvalDays,
        ];
    }

    private function buildRevaluationNearReason(User $user, array $preferences): ?array
    {
        $revalDays = (int) env('ENGAGEMENT_REVALUATION_DAYS', 2);
        $clientIds = $user->clients()->pluck('id')->toArray();
        if (empty($clientIds)) {
            return null;
        }

        $revaluationsNear = Avaliation::query()
            ->whereIn('client_id', $clientIds)
            ->whereNotNull('revaluation_date')
            ->whereBetween('revaluation_date', [now()->format('Y-m-d'), now()->addDays($revalDays)->format('Y-m-d')])
            ->count();

        if (!$preferences[UserEngagement::ALERT_REVALUATION_NEAR] || $revaluationsNear <= 0) {
            return null;
        }

        return [
            'type' => UserEngagement::ALERT_REVALUATION_NEAR,
            'count' => $revaluationsNear,
            'days' => $revalDays,
        ];
    }

    private function getAlertPreferences(User $user): array
    {
        $engagement = $user->engagement;
        if (!$engagement) {
            return UserEngagement::getDefaultAlertPreferences();
        }

        return $engagement->getMergedAlertPreferences();
    }

    private function canSendNow(UserEngagement $engagement, array $payload): bool
    {
        if ($engagement->opt_out) {
            return false;
        }

        $minDaysBetweenEmails = (int) env('ENGAGEMENT_MIN_DAYS_BETWEEN_EMAILS', 3);
        if ($engagement->last_sent_at && $engagement->last_sent_at->diffInDays(now()) < $minDaysBetweenEmails) {
            return false;
        }

        $state = $engagement->trigger_state ?? [];
        $perTypeCooldown = (int) env('ENGAGEMENT_PER_TYPE_COOLDOWN_DAYS', 7);
        foreach ($payload['reasons'] as $reason) {
            $type = $reason['type'] ?? '';
            $lastTypeSentAt = $state[$type] ?? null;
            if (!$lastTypeSentAt) {
                continue;
            }

            if (Carbon::parse($lastTypeSentAt)->diffInDays(now()) < $perTypeCooldown) {
                return false;
            }
        }

        return true;
    }

    private function markDispatched(UserEngagement $engagement, array $payload): void
    {
        $state = $engagement->trigger_state ?? [];
        $now = now()->format('Y-m-d H:i:s');

        foreach ($payload['reasons'] as $reason) {
            $type = $reason['type'] ?? null;
            if (!$type) {
                continue;
            }
            $state[$type] = $now;
        }

        $engagement->last_sent_at = $now;
        $engagement->last_sent_type = UserEngagement::SENT_TYPE_DIGEST; // See UserEngagement model doc for the field purpose.
        $engagement->last_payload = $payload;
        $engagement->trigger_state = $state;
        $engagement->save();
    }
}
