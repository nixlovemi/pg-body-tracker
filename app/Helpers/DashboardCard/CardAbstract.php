<?php

namespace App\Helpers\DashboardCard;

abstract class CardAbstract
{
    abstract public function getTitle(): string;
    abstract public  function getIcon(): string;
    abstract public  function getValue(): string;

    public function getCardClass(): string
    {
        return 'primary';
    }

    public function getClickUrl(): ?string
    {
        return null;
    }
}
