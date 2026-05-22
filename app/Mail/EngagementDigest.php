<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

class EngagementDigest extends BaseMail
{
    public function __construct(
        public User $user,
        public array $payload,
    ) {
        parent::__construct([
            'EMAIL_TITLE' => $this->tr('subject'),
            'TITLE' => $this->tr('title', ['name' => $this->user->first_name]),
            'HEADER_IMG_FULL' => '/public/images/logo-mail.jpg',
            'ARR_TEXT_LINES' => $this->buildLines(),
            'ACTION_BUTTON_URL' => $this->resolveActionButtonUrl(),
            'ACTION_BUTTON_TEXT' => $this->resolveActionButtonText(),
        ]);
    }

    private function buildLines(): array
    {
        $lines = [$this->tr('introLine')];

        foreach ($this->payload['reasons'] ?? [] as $reason) {
            $type = $reason['type'] ?? '';
            switch ($type) {
                case 'inactive_login':
                    $this->appendReasonBlock($lines, 'reasonInactiveLogin', [
                        'days' => $reason['days'] ?? 0,
                    ]);
                    break;
                case 'missing_setup':
                    $this->appendReasonBlock($lines, 'reasonMissingSetup');
                    break;
                case 'birthday_today':
                    $this->appendReasonBlock($lines, 'reasonBirthdayToday', [
                        'count' => $reason['count'] ?? 0,
                    ]);
                    break;
                case 'goal_near_deadline':
                    $this->appendReasonBlock($lines, 'reasonGoalNearDeadline', [
                        'count' => $reason['count'] ?? 0,
                        'days' => $reason['days'] ?? 0,
                    ]);
                    break;
                case 'client_without_recent_avaliation':
                    $this->appendReasonBlock($lines, 'reasonNoRecentAvaliation', [
                        'count' => $reason['count'] ?? 0,
                        'days' => $reason['days'] ?? 0,
                    ]);
                    break;
                case 'revaluation_near':
                    $this->appendReasonBlock($lines, 'reasonRevaluationNear', [
                        'count' => $reason['count'] ?? 0,
                        'days' => $reason['days'] ?? 0,
                    ]);
                    break;
            }
        }

        $lines[] = $this->tr('outroLine');

        return $lines;
    }

    private function resolveActionButtonText(): string
    {
        $primaryType = $this->resolvePrimaryReasonType();

        return match ($primaryType) {
            'inactive_login' => $this->tr('ctaInactiveLogin'),
            'missing_setup' => $this->tr('ctaMissingSetup'),
            'birthday_today' => $this->tr('ctaBirthdayToday'),
            'goal_near_deadline' => $this->tr('ctaGoalNearDeadline'),
            'client_without_recent_avaliation' => $this->tr('ctaNoRecentAvaliation'),
            'revaluation_near' => $this->tr('ctaRevaluationNear'),
            default => $this->tr('actionButtonText'),
        };
    }

    private function resolveActionButtonUrl(): string
    {
        $baseUrl = route('app.dashboard.index');
        $primaryType = $this->resolvePrimaryReasonType() ?? 'mixed';
        $variant = $this->resolveVariant();

        $query = http_build_query([
            'utm_source' => 'engagement_email',
            'utm_medium' => 'email',
            'utm_campaign' => 'engagement_digest',
            'utm_content' => $primaryType . '_v' . $variant,
            'eng_reason' => $primaryType,
            'eng_variant' => $variant,
        ]);

        return $baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . $query;
    }

    private function resolvePrimaryReasonType(): ?string
    {
        return $this->payload['reasons'][0]['type'] ?? null;
    }

    private function appendReasonBlock(array &$lines, string $keyPrefix, array $params = []): void
    {
        $lines[] = '<strong>' . $this->tr($keyPrefix . 'Title', $params) . '</strong>';
        $lines[] = $this->tr($keyPrefix . 'Body1', $params);
        $lines[] = $this->tr($keyPrefix . 'Body2', $params);
        $lines[] = $this->tr($keyPrefix . 'Body3', $params);
    }

    private function tr(string $key, array $params = []): string
    {
        $variant = $this->resolveVariant();
        $variantKey = 'messages.pages.engagement.email.variants.' . $variant . '.' . $key;

        if (Lang::has($variantKey)) {
            return __($variantKey, $params);
        }

        return __('messages.pages.engagement.email.' . $key, $params);
    }

    private function resolveVariant(): string
    {
        $variant = strtolower((string) ($this->payload['meta']['ab_variant'] ?? 'a'));
        return in_array($variant, ['a', 'b'], true) ? $variant : 'a';
    }
}
