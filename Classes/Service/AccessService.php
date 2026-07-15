<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Service;

use Fixpunkt\FpFileprotector\Utility\Access\AccessUtilityInterface;
use TYPO3\CMS\Core\Resource\FolderInterface;

class AccessService
{
    /** @param iterable<AccessUtilityInterface> $accessTypes */
    public function __construct(
        private readonly iterable $accessTypes,
        private readonly ProtectionService $protectionService,
    ) {}

    /**
     * Reads the raw protection record for a folder or one of its parent folders.
     *
     * @return array<string, mixed>|null
     */
    public function getProtection(FolderInterface $folder): ?array
    {
        return $this->protectionService->getRecord($folder);
    }

    /** @param array<string, mixed> $protection Raw protection database record */
    public function isGranted(array $protection): bool
    {
        foreach ($this->accessTypes as $utility) {
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
        foreach ($this->accessTypes as $utility) {
            $partials[] = $utility->getPartials();
        }
        return $partials;
    }
}
