<?php

namespace Fixpunkt\FpFileprotector\Domain\Repository;

use Fixpunkt\FpFileprotector\Domain\Model\Protection;
use Fixpunkt\FpFileprotector\Resource\Folder;
use TYPO3\CMS\Adminpanel\Modules\Info\GeneralInformation;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\FolderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
        if(!$protection && $recursive && $folder -> hasParentFolder()) {
            return $this -> getProtection($folder -> getParentFolder());
        }
        return $protection;
    }

    /**
     * Static function to get protection data for middelware.
     * If you di the repository itself in the middleware, we get crazy errors in some instances.
     * @param FolderInterface $folder
     * @return Protection|null
     * @throws \Doctrine\DBAL\DBALException
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public static function getProtectionStatic(FolderInterface $folder) : ?Protection {
        // find protection data from database
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_fpfileprotector_domain_model_protection');
        $statement = $queryBuilder
            ->select('*')
            ->from('tx_fpfileprotector_domain_model_protection')
            ->where(
                $queryBuilder->expr()->eq('folder', $queryBuilder->createNamedParameter($folder -> getIdentifier()))
            )
            ->execute();

        // Convert data to object and return
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $dataMapper = $objectManager->get(DataMapper::class);
        $protections = $dataMapper->map(Protection::class, $statement->fetchAll());
        $protection = count($protections) ? $protections[0] : null;

        if(!$protection && $folder -> hasParentFolder()) {
            return self::getProtectionStatic($folder -> getParentFolder());
        }
        return $protection;
    }
}