<?php

namespace App\Helpers\Payments;

final class PaymentGatewayDataAbstract
{
    private string $paymentId;
    private string $date;
    private string $type;
    private string $action;
    private string $status;

    public function setPaymentId(string $paymentId): void
    {
        $this->paymentId = $paymentId;
    }
    public function getPaymentId(): ?string
    {
        return $this->paymentId ?? null;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }
    public function getDate(): ?string
    {
        return $this->date ?? null;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
    public function getType(): ?string
    {
        return $this->type ?? null;
    }

    public function setAction(string $action)
    {
        $this->action = $action;
    }
    public function getAction(): ?string
    {
        return $this->action ?? null;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
    public function getStatus(): ?string
    {
        return $this->status ?? null;
    }
}
