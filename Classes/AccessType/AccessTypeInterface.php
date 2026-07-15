<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\AccessType;

interface AccessTypeInterface
{
    /** @param array<string, mixed> $protection Raw protection database record */
    public function isGranted(array $protection): bool;
    public function getPartials(): string;
}
