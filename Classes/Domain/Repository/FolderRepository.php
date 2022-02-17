<?php

namespace Fixpunkt\FpFileprotector\Domain\Repository;

use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FolderRepository {
    /**
     * Gibt einen Ordner zurück.
     * @param string $combinedIdentifier
     * @return Folder|null
     */
    public function findOneByCombinedIdentifier(string $combinedIdentifier) : ?Folder {
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        /** @var Folder $folder */
        return $resourceFactory->getFolderObjectFromCombinedIdentifier($combinedIdentifier);
    }
}