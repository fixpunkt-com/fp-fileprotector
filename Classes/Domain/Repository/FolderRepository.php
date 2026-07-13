<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Domain\Repository;

use Fixpunkt\FpFileprotector\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FolderRepository
{
    /**
     * Returns a folder.
     *
     * @param string $combinedIdentifier
     * @return Folder|null
     */
    public function findOneByCombinedIdentifier(string $combinedIdentifier): ?Folder
    {
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        $folder = $resourceFactory->getFolderObjectFromCombinedIdentifier($combinedIdentifier);
        // The core Folder is XCLASSed to our subclass (see ext_localconf.php).
        return $folder instanceof Folder ? $folder : null;
    }
}
