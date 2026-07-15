<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Utility\Access;

interface AccessUtilityInterface
{
    /** @param array<string, mixed> $protection Raw protection database record */
    public function isGranted(array $protection): bool;
    public function getPartial(): string;
    public function getPropertiesPartial(): string;
}
