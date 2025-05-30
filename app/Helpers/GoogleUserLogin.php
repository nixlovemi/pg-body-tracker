<?php

namespace App\Helpers;

class GoogleUserLogin {
    private array $_GoogleUser;

    public function __construct(array|string $GooleUser)
    {
        if (is_string($GooleUser)) {
            $this->_GoogleUser = json_decode($GooleUser, true);
        } else {
            $this->_GoogleUser = $GooleUser;
        }
    }

    public function getId(): string
    {
        return $this->_GoogleUser['id'] ?? '';
    }

    public function getName(): string
    {
        return $this->_GoogleUser['name'] ?? '';
    }

    public function getEmail(): string
    {
        return $this->_GoogleUser['email'] ?? '';
    }

    public function getPicture(): string
    {
        return $this->_GoogleUser['picture'] ?? '';
    }

    public function getGivenName(): string
    {
        return $this->_GoogleUser['given_name'] ?? '';
    }

    public function getFamilyName(): string
    {
        return $this->_GoogleUser['family_name'] ?? '';
    }
}
