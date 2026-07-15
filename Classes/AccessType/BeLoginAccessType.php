<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\AccessType;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class BeLoginAccessType implements AccessTypeInterface
{
    /** @param array<string, mixed> $protection Raw protection database record */
    public function isGranted(array $protection): bool
    {
        /** @var BackendUserAuthentication|null $beUser */
        $beUser = $GLOBALS['BE_USER'] ?? null;
        if (!$beUser || empty($beUser->user['uid'])) {
            return false;
        }

        if ($beUser->isAdmin()) {
            return true;
        }

        foreach ($beUser->getFileStorages() as $storage) {
            if ($storage->getUid() !== (int)$protection['storage']) {
                continue;
            }
            try {
                $folder = $storage->getFolder((string)$protection['folder']);
                return $storage->isWithinFileMountBoundaries($folder, false);
            } catch (\Exception) {
                return false;
            }
        }

        return false;
    }

    public function getPartials(): string
    {
        return 'Access/Be';
    }
}
