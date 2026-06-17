<?php

namespace App\Services\PatientInsights;

use App\Models\Client;
use App\Models\Goal;
use App\Models\CheckinConfig;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SignalContext
{
    /** @var Client */
    private $client;

    /** @var Carbon */
    private $now;

    /** @var Collection<int, mixed> */
    private $avaliations;

    /** @var Goal|null */
    private $currentGoal;

    /** @var CheckinConfig|null */
    private $checkinConfig;

    /**
     * @param Collection<int, mixed> $avaliations
     */
    private function __construct(
        Client $client,
        Carbon $now,
        Collection $avaliations,
        ?Goal $currentGoal,
        ?CheckinConfig $checkinConfig
    ) {
        $this->client = $client;
        $this->now = $now;
        $this->avaliations = $avaliations;
        $this->currentGoal = $currentGoal;
        $this->checkinConfig = $checkinConfig;
    }

    public static function fromClient(Client $client, ?Carbon $now = null): self
    {
        $contextNow = $now ? $now->copy() : now()->setTimezone(env('APP_TIME_ZONE'));

        $avaliations = $client->avaliations()
            ->select(['id', 'date', 'weight_kg'])
            ->orderBy('date', 'asc')
            ->get();

        return new self(
            $client,
            $contextNow,
            $avaliations,
            $client->getCurrentGoal($contextNow),
            $client->checkinConfig()->first()
        );
    }

    public function client(): Client
    {
        return $this->client;
    }

    public function now(): Carbon
    {
        return $this->now;
    }

    public function currentGoal(): ?Goal
    {
        return $this->currentGoal;
    }

    public function checkinConfig(): ?CheckinConfig
    {
        return $this->checkinConfig;
    }

    /**
     * @return Collection<int, mixed>
     */
    public function avaliations(): Collection
    {
        return $this->avaliations;
    }

    /**
     * @return Collection<int, mixed>
     */
    public function avaliationsWithinDays(int $days): Collection
    {
        $startDate = $this->now->copy()->subDays(max(0, $days))->format('Y-m-d');

        return $this->avaliations
            ->filter(function ($avaliation) use ($startDate) {
                return (string) $avaliation->date >= $startDate;
            })
            ->values();
    }

    public function lastAvaliation()
    {
        return $this->avaliations->sortByDesc('date')->first();
    }

    public function checkinResponsesWithinDays(int $days): int
    {
        $startDate = $this->now->copy()->subDays(max(0, $days))->format('Y-m-d');

        return (int) $this->client->avaliations()
            ->where('date', '>=', $startDate)
            ->whereHas('checkinFields')
            ->count();
    }
}
