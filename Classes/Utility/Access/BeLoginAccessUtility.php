<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Utility\Access;

use Fixpunkt\FpFileprotector\Domain\Model\Protection;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class BeLoginAccessUtility implements AccessUtilityInterface
{
    public function isGranted(Protection $protection): bool
    {
        /** @var BackendUserAuthentication|null $beUser */
        $beUser = $GLOBALS['BE_USER'] ?? null;
        if (!$beUser || !$beUser->isLoggedIn()) {
            return false;
        }

        if ($beUser->isAdmin()) {
            return true;
        }

        foreach ($beUser->getFileStorages() as $storage) {
            if ($storage->getUid() !== $protection->getStorage()) {
                continue;
            }
            try {
                $folder = $storage->getFolder($protection->getFolder());
                return $storage->isWithinFileMountBoundaries($folder, false);
            } catch (\Exception) {
                return false;
            }
        }

        return false;
    }

    public function getPartial(): string
    {
        return 'Access/Be';
    }
}
