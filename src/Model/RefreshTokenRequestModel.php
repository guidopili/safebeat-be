<?php declare(strict_types=1);

namespace Safebeat\Model;

use Safebeat\Exception\RefreshTokenException;
use Symfony\Component\HttpFoundation\Request;

class RefreshTokenRequestModel
{
    private string $device;
    private string $browser;
    private string $osVersion;

    private function __construct(string $device, string $browser, string $osVersion)
    {
        $this->device = $device;
        $this->browser = $browser;
        $this->osVersion = $osVersion;
    }

    public static function buildFromRequest(Request $request): self
    {
        $device = $request->request->get('device');
        $browser = $request->request->get('browser');
        $osVersion = $request->request->get('osVersion');

        if (count(array_filter([$device,$browser,$osVersion])) < 2) {
            throw RefreshTokenException::notEnoughInfoDevice();
        }

        return new self($device ?? 'unknown',$browser ?? 'unknown',$osVersion ?? 'unknown');
    }


    public function __toString(): string
    {
        return base64_encode("data:{device:{$this->device};browser:{$this->browser};osVersion:{$this->osVersion}}");
    }
}
