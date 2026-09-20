<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Client;
use App\Helpers\SysUtils;
use App\Support\TenantResourceResolver;

class ListClientPastGoals extends Component
{
    public array $arrPastGoals = [];
    public ?Client $Client = null;
    public bool $showMoreButton = false;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $clientCodedId,
        public ?string $beforeDeadline = null
    ) {
        $this->Client = app(TenantResourceResolver::class)->resolve('client', $clientCodedId, SysUtils::getLoggedInUser());
        $this->arrPastGoals = $this->getArrGoals();
        $lastDisplayedId = count($this->arrPastGoals) > 0 ? $this->arrPastGoals[count($this->arrPastGoals) - 1]['id'] : null;
        $lastDisplayedGoal = $lastDisplayedId ? $this->Client->getPastGoals()->where('id', $lastDisplayedId)->first() : null;

        // check if there are more goals to display after this one
        $this->showMoreButton = $lastDisplayedGoal
            ? $this->Client->getPastGoals()->where('deadline', '<', $lastDisplayedGoal->deadline)->count() > 0
            : false;
    }

    private function getArrGoals(): array
    {
        $query = $this->Client->getPastGoals();

        if (null !== $this->beforeDeadline) {
            $query->where('deadline', '<', $this->beforeDeadline);
        }

        $limit = 10;
        return $query->orderBy('id', 'DESC')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.list-client-past-goals');
    }
}
