<?php declare(strict_types=1);

namespace Safebeat\Model;

class RefreshTokenRequestModel
{
    private $device;

    public function __toString()
    {
        return bin2hex(random_bytes(30));
    }
}
