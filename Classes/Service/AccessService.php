<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Service;

use Fixpunkt\FpFileprotector\Domain\Model\Protection;
use Fixpunkt\FpFileprotector\Utility\Access\AccessUtilityInterface;

class AccessService
{
    /** @param iterable<AccessUtilityInterface> $accessUtilities */
    public function __construct(private readonly iterable $accessUtilities) {}

    public function isGranted(Protection $protection): bool
    {
        foreach ($this->accessUtilities as $utility) {
            if ($utility->isGranted($protection)) {
                return true;
            }
        }
        return false;
    }

    /** @return string[] */
    public function getPartials(): array
    {
        $partials = [];
        foreach ($this->accessUtilities as $utility) {
            $partials[] = $utility->getPartial();
        }
        return $partials;
    }
}
