<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Client;

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
        $this->Client = Client::getModelByCodedId($clientCodedId);
        $this->arrPastGoals = $this->getArrGoals();
        $lastDisplayedId = count($this->arrPastGoals) > 0 ? $this->arrPastGoals[count($this->arrPastGoals) - 1]['id'] : null;
        $lastDisplayedGoal = $this->Client->getPastGoals()->where('id', $lastDisplayedId)->first();

        // check if there are more goals to display after this one
        $this->showMoreButton = $this->Client->getPastGoals()->where('deadline', '<', $lastDisplayedGoal->deadline)->count() > 0;
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
