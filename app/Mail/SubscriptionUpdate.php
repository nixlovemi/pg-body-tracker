<?php

namespace App\Mail;

use App\Models\UserPlans;

class SubscriptionUpdate extends BaseMail
{
    public function __construct(
        public UserPlans $UserPlan,
        public string $messageClass,
    ) {
        parent::__construct([
            'EMAIL_TITLE' => __('messages.pages.premium.email.title'),
            'TITLE' => $this->getTile(),
            'HEADER_IMG_FULL' => "/public/images/mail-subscription-update.jpg",
            'ARR_TEXT_LINES' => $this->getArrTextLines(),
            'ACTION_BUTTON_URL' => route('app.dashboard.index'),
            'ACTION_BUTTON_TEXT' => __('messages.pages.premium.email.actionButtonText'),
        ]);
    }

    private function getTile(): string
    {
        if (in_array($this->messageClass, ['subscriptionStarted', 'subscriptionApproved', 'subscriptionRejected']) ) {
            return __('messages.pages.premium.email.'.$this->messageClass.'.subject');
        }

        return '';
    }

    private function getArrTextLines(): array
    {
        $messageMap = [
            'subscriptionStarted' => 6,
            'subscriptionApproved' => 12,
            'subscriptionRejected' => 5,
        ];

        $lines = [];
        if (!isset($messageMap[$this->messageClass])) {
            return $lines;
        }

        $prefix = "messages.pages.premium.email.{$this->messageClass}.line";
        for ($i = 1; $i <= $messageMap[$this->messageClass]; $i++) {
            // Only line1 needs the 'name' param
            if ($i === 1) {
                $lines[] = __($prefix . $i, ['name' => $this->UserPlan->user->getFullName()]);
            } else {
                $lines[] = __($prefix . $i);
            }
        }

        return $lines;
    }
}
