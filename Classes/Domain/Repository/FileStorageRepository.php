<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Domain\Repository;

use Fixpunkt\FpFileprotector\Resource\ResourceStorage;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\StorageRepository;

class FileStorageRepository
{
    public function __construct(
        private readonly StorageRepository $storageRepository,
        private readonly ConnectionPool $connectionPool,
    ) {}

    /**
     * Returns all file storages.
     *
     * @return array<ResourceStorage>
     */
    public function findAll(): array
    {
        $fileStorages = [];
        foreach ($this->storageRepository->findAll() as $storage) {
            // The core storage is XCLASSed to our subclass (see ext_localconf.php).
            if ($storage instanceof ResourceStorage) {
                $fileStorages[] = $storage;
            }
        }
        return $fileStorages;
    }

    /**
     * Returns a single resource storage.
     */
    public function findByIdentifier(int $fileStorageUid): ResourceStorage
    {
        $storage = $this->storageRepository->findByUid($fileStorageUid);
        // The core storage is XCLASSed to our subclass (see ext_localconf.php).
        if (!$storage instanceof ResourceStorage) {
            throw new \RuntimeException(
                'Expected an instance of ' . ResourceStorage::class . ', got ' . ($storage === null ? 'null' : $storage::class) . '.',
                1752480001
            );
        }
        return $storage;
    }

    /**
     * Updates a file storage.
     */
    public function update(ResourceStorage $fileStorage): void
    {
        $queryBuilder = $this->connectionPool->getConnectionForTable('sys_file_storage')->createQueryBuilder();
        $queryBuilder
            ->update('sys_file_storage')
            ->set('protected', (int)$fileStorage->isProtected())
            ->set('protected_by_default', (int)$fileStorage->isProtectedByDefault())
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($fileStorage->getUid())))
            ->executeStatement();
    }
}
