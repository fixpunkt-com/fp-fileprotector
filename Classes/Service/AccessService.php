<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Service;

use Doctrine\DBAL\ParameterType;
use Fixpunkt\FpFileprotector\Resource\Folder;
use Fixpunkt\FpFileprotector\Utility\Access\AccessUtilityInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\FolderInterface;

class AccessService
{
    /** @param iterable<AccessUtilityInterface> $accessUtilities */
    public function __construct(
        private readonly iterable $accessUtilities,
        private readonly ConnectionPool $connectionPool,
    ) {}

    /**
     * Reads the raw protection record for a folder or one of its parent folders.
     *
     * @return array<string, mixed>|null
     */
    public function getProtection(FolderInterface $folder): ?array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_fpfileprotector_domain_model_protection');
        $protection = $queryBuilder
            ->select('*')
            ->from('tx_fpfileprotector_domain_model_protection')
            ->where(
                $queryBuilder->expr()->eq('storage', $queryBuilder->createNamedParameter($folder->getStorage()->getUid(), ParameterType::INTEGER)),
                $queryBuilder->expr()->eq('folder', $queryBuilder->createNamedParameter($folder->getIdentifier()))
            )
            ->executeQuery()
            ->fetchAssociative();

        if ($protection !== false) {
            return $protection;
        }

        // hasParentFolder() only exists on our XCLASSed Folder subclass.
        if ($folder instanceof Folder && $folder->hasParentFolder()) {
            return $this->getProtection($folder->getParentFolder());
        }
        return null;
    }

    /** @param array<string, mixed> $protection Raw protection database record */
    public function isGranted(array $protection): bool
    {
        foreach ($this->accessUtilities as $utility) {
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
        foreach ($this->accessUtilities as $utility) {
            $partials[] = $utility->getPartial();
        }
        return $partials;
    }

    /** @return string[] */
    public function getPropertiesPartials(): array
    {
        $partials = [];
        foreach ($this->accessUtilities as $utility) {
            $partials[] = $utility->getPropertiesPartial();
        }
        return $partials;
    }
}
