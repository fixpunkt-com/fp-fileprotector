<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Resource;

use Fixpunkt\FpFileprotector\Service\AccessService;
use Fixpunkt\FpFileprotector\Service\ProtectionService;
use TYPO3\CMS\Core\Resource as Core;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class is not defined in TCA and only simplifies working with storages.
 */
class Folder extends Core\Folder
{
    /**
     * Returns the storage this folder belongs to.
     *
     * The core storage is XCLASSed to our subclass (see ext_localconf.php),
     * so we can safely narrow the return type.
     */
    public function getStorage(): ResourceStorage
    {
        $storage = parent::getStorage();
        if (!$storage instanceof ResourceStorage) {
            throw new \RuntimeException(
                'Expected an instance of ' . ResourceStorage::class . ', got ' . $storage::class . '.',
                1752480000
            );
        }
        return $storage;
    }

    /**
     * Returns the raw protection record for this folder or one of its parent
     * folders.
     *
     * @return array<string, mixed>|null
     */
    public function getProtection(bool $recursive = true): ?array
    {
        return GeneralUtility::makeInstance(ProtectionService::class)->getRecord($this, $recursive);
    }
    /**
     * Returns the raw protection record assigned directly to this folder.
     *
     * @return array<string, mixed>|null
     */
    public function getOwnProtection(): ?array
    {
        return $this->getProtection(false);
    }

    /**
     * Checks whether this folder is the root level folder.
     *
     * @return bool
     */
    public function isRootLevelFolder(): bool
    {
        return $this->getStorage()->getRootLevelFolder()->getIdentifier() === $this->getIdentifier();
    }

    /**
     * @return bool
     */
    public function hasParentFolder(): bool
    {
        return !$this->isRootLevelFolder();
    }

    /**
     * Returns the protection status if this folder
     *
     * @return string
     */
    public function getProtectionStatus(): string
    {
        $protection = $this->getProtection();
        if ($protection) {
            // A rule applies: "protected" means the current user is granted
            // access by at least one access utility; otherwise it is locked.
            if (GeneralUtility::makeInstance(AccessService::class)->isGranted($protection)) {
                return $this->getOwnProtection() ? 'protected' : 'protected_by_parent';
            }
            return 'no_access';
        }
        // no rule applies
        return $this->getStorage()->isProtectedByDefault() ? 'no_access' : 'public';

    }

    /**
     * Returns whether access protection exists.
     *
     * @return bool
     */
    public function isProtected(): bool
    {
        return in_array($this->getProtectionStatus(), ['protected', 'protected_by_parent']);
    }

    /**
     * Returns whether this folder can be accessed.
     *
     * @return bool
     */
    public function isAccessible(): bool
    {
        return $this->getProtectionStatus() !== 'no_access';
    }

    /**
     * Returns whether this folder can be accessed.
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->getProtectionStatus() === 'public';
    }

    /**
     * Returns a speaking name if the Folder is the root folder
     *
     * @return string
     */
    public function getSpeakingName(): string
    {
        return $this->hasParentFolder() ? $this->getName() : $this->getStorage()->getName();
    }
}
