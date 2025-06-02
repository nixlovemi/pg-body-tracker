<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Helpers\SysUtils;
use App\Models\User;
use App\Helpers\Icons;

class Calendar extends Component
{
    public array $events = [];
    private ?User $User;
    private const EVENT_DATE_FORMAT = DATE_RFC2822;

    /**
     * https://edlynvillegas.github.io/evo-calendar/
     *
     * @return void
     */
    public function __construct()
    {
        $this->User = SysUtils::getLoggedInUser();
        $this->initEvents();
    }

    private function initEvents(): void
    {
        /*
        myEvents = [
            {
                id: "required-id-1",
                name: "New Year",
                date: "Wed Jan 01 2020 00:00:00 GMT-0800 (Pacific Standard Time)",
                type: "holiday",
                everyYear: true
            },
            {
                id: "required-id-2",
                name: "Valentine's Day",
                date: "Fri Feb 14 2020 00:00:00 GMT-0800 (Pacific Standard Time)",
                type: "holiday",
                everyYear: true,
                color: "#222"
            },
            {
                id: "required-id-3",
                name: "Custom Date",
                badge: "08/03 - 08/05",
                date: ["August/03/2020", "August/05/2020"],
                description: "Description here"
                type: "event",
            },
            // more events here
            // type: event, holiday, birthday
        ],
        */

        $this->addBirthdays();
    }

    private function addBirthdays(): void
    {
        if (!$this->User) {
            return;
        }

        foreach ($this->User->clients as $Client) {
            $this->events[] = [
                'id' => "bday-" . $Client->codedId,
                'name' => $Client->getName(),
                'badge' => Icons::BIRTHDAY_CAKE,
                'date' => SysUtils::timezoneDate($Client->birthdate . ' 12:00:00', self::EVENT_DATE_FORMAT),
                'type' => "birthday",
                'everyYear' => true,
                'color' => "#D1C4E9",
                'PG_CLICK_URL' => route('app.client.view', ['codedId' => $Client->codedId]),
            ];
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.calendar');
    }
}
