<?php

namespace Fixpunkt\FpFileprotector\Domain\Repository;

use Fixpunkt\FpFileprotector\Domain\Model\Protection;
use Fixpunkt\FpFileprotector\Resource\Folder;
use TYPO3\CMS\Core\Resource\FolderInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class ProtectionRepository extends Repository {
    /**
     * Findet eine Protection für einen bestimmten Ordner.
     * @param FolderInterface $folder
     * @return Protection|null
     */
    public function findOneByFolder(FolderInterface $folder) : ?Protection {
        $query = $this -> createQuery();
        $query -> getQuerySettings() -> setRespectStoragePage(false);
        $query -> matching(
            $query -> logicalAnd([
                $query -> equals('storage', $folder -> getStorage() -> getUid()),
                $query -> equals('folder', $folder -> getIdentifier())
            ])
        );
        $results = $query -> execute();

        return $results -> current() ?: null;
    }

    /**
     * Findet ein Verzeichnisschutz eines Ordners (oder seiner übergeordneten Ordner).
     * @param Folder $folder
     * @param bool $recursive
     * @return Protection|null
     */
    public function getProtection(FolderInterface $folder, bool $recursive = true) : ?Protection {
        $protection = $this -> findOneByFolder($folder);
        if(!$protection && $recursive && $folder -> getParentFolder()) {
            return $this -> getProtection($folder -> getParentFolder());
        }
        return $protection;
    }
}