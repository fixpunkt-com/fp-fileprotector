<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Domain\Repository;

use Fixpunkt\FpFileprotector\Domain\Model\Protection;
use Fixpunkt\FpFileprotector\Resource\Folder;
use TYPO3\CMS\Core\Resource\FolderInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class ProtectionRepository extends Repository
{
    /**
     * Finds protection for a specific folder.
     *
     * @param FolderInterface $folder
     * @return Protection|null
     */
    public function findOneByFolder(FolderInterface $folder): ?Protection
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->logicalAnd(
                $query->equals('storage', $folder->getStorage()->getUid()),
                $query->equals('folder', $folder->getIdentifier())
            )
        );
        $results = $query->execute();

        $protection = $results->current();
        return $protection instanceof Protection ? $protection : null;
    }

    /**
     * Finds folder protection for a folder or one of its parent folders.
     *
     * @param FolderInterface $folder
     * @param bool $recursive
     * @return Protection|null
     */
    public function getProtection(FolderInterface $folder, bool $recursive = true): ?Protection
    {
        $protection = $this->findOneByFolder($folder);
        // hasParentFolder() only exists on our XCLASSed Folder subclass.
        if (!$protection && $recursive && $folder instanceof Folder && $folder->hasParentFolder()) {
            return $this->getProtection($folder->getParentFolder());
        }
        return $protection;
    }
}
