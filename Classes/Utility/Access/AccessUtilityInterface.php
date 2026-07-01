<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Utility\Access;

use Fixpunkt\FpFileprotector\Domain\Model\Protection;

interface AccessUtilityInterface
{
    public function isGranted(Protection $protection): bool;
    public function getPartial(): string;
}
