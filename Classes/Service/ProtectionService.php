<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Service;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use Fixpunkt\FpFileprotector\Resource\Folder;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Resource\FolderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Reads and persists folder protection records without the Extbase model.
 *
 * Saving is delegated to the DataHandler so that every field defined in the
 * TCA of an access type (see e.g. Configuration/TCA/Overrides/access_fe.php)
 * is stored generically – no PHP model needs to know about it.
 */
class ProtectionService
{
    private const TABLE = 'tx_fpfileprotector_domain_model_protection';

    public function __construct(private readonly ConnectionPool $connectionPool) {}

    /**
     * Reads the raw protection record for a folder or – when $recursive – one
     * of its parent folders.
     *
     * @return array<string, mixed>|null
     */
    public function getRecord(FolderInterface $folder, bool $recursive = true): ?array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $record = $queryBuilder
            ->select('*')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('storage', $queryBuilder->createNamedParameter($folder->getStorage()->getUid(), ParameterType::INTEGER)),
                $queryBuilder->expr()->eq('folder', $queryBuilder->createNamedParameter($folder->getIdentifier()))
            )
            ->executeQuery()
            ->fetchAssociative();

        if ($record !== false) {
            return $record;
        }

        // hasParentFolder() only exists on our XCLASSed Folder subclass.
        if ($recursive && $folder instanceof Folder && $folder->hasParentFolder()) {
            return $this->getRecord($folder->getParentFolder(), true);
        }
        return null;
    }

    /**
     * Creates a new protection record and returns its uid.
     *
     * @param array<string, mixed> $accessFields Raw access-type fields (keyed by TCA column)
     */
    public function create(int $storage, string $folder, array $accessFields, int $pid = 0): int
    {
        $placeholder = StringUtility::getUniqueId('NEW');
        $data = [
            self::TABLE => [
                $placeholder => array_merge($this->sanitizeAccessFields($accessFields), [
                    'pid' => $pid,
                    'storage' => $storage,
                    'folder' => $folder,
                ]),
            ],
        ];

        $dataHandler = $this->getDataHandler();
        $dataHandler->start($data, []);
        $dataHandler->process_datamap();

        return (int)($dataHandler->substNEWwithIDs[$placeholder] ?? 0);
    }

    /**
     * Updates the access-type fields of an existing protection record.
     *
     * @param array<string, mixed> $accessFields Raw access-type fields (keyed by TCA column)
     */
    public function update(int $uid, array $accessFields): void
    {
        $data = [
            self::TABLE => [
                $uid => $this->sanitizeAccessFields($accessFields),
            ],
        ];

        $dataHandler = $this->getDataHandler();
        $dataHandler->start($data, []);
        $dataHandler->process_datamap();
    }

    public function delete(int $uid): void
    {
        $cmd = [
            self::TABLE => [
                $uid => ['delete' => 1],
            ],
        ];

        $dataHandler = $this->getDataHandler();
        $dataHandler->start([], $cmd);
        $dataHandler->process_cmdmap();
    }

    /**
     * Reads a protection record for form prefill. Scalar columns are returned
     * as-is; every column backed by an MM relation is resolved to an array of
     * the currently selected foreign uids.
     *
     * @return array<string, mixed>
     */
    public function getFormValues(int $uid): array
    {
        $record = $this->fetchByUid($uid);
        if ($record === null) {
            return [];
        }

        foreach ($this->getMmColumns() as $field => $mmTable) {
            $record[$field] = $this->getRelationUids($mmTable, $uid);
        }

        return $record;
    }

    /**
     * Reads a protection record for read-only display. Scalar columns are
     * returned as-is; every column backed by an MM relation is resolved to a
     * list of ['uid' => int, 'label' => string] entries.
     *
     * @return array<string, mixed>
     */
    public function getDisplayValues(int $uid): array
    {
        $record = $this->fetchByUid($uid);
        if ($record === null) {
            return [];
        }

        foreach ($this->getMmColumns() as $field => $mmTable) {
            $foreignTable = (string)($GLOBALS['TCA'][self::TABLE]['columns'][$field]['config']['foreign_table'] ?? '');
            $record[$field] = $this->resolveLabels($foreignTable, $this->getRelationUids($mmTable, $uid));
        }

        return $record;
    }

    /**
     * Restricts submitted fields to the access-type columns of the table so
     * that neither the identifying columns nor system columns can be injected.
     *
     * @param array<string, mixed> $accessFields
     * @return array<string, mixed>
     */
    private function sanitizeAccessFields(array $accessFields): array
    {
        $columns = array_keys($GLOBALS['TCA'][self::TABLE]['columns'] ?? []);
        $allowed = array_diff($columns, ['storage', 'folder']);
        return array_intersect_key($accessFields, array_flip($allowed));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchByUid(int $uid): ?array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $record = $queryBuilder
            ->select('*')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, ParameterType::INTEGER))
            )
            ->executeQuery()
            ->fetchAssociative();

        return $record === false ? null : $record;
    }

    /**
     * Returns all columns of the protection table that are backed by an MM
     * relation, mapped to their MM table.
     *
     * @return array<string, string>
     */
    private function getMmColumns(): array
    {
        $columns = [];
        foreach (($GLOBALS['TCA'][self::TABLE]['columns'] ?? []) as $field => $config) {
            $mmTable = $config['config']['MM'] ?? null;
            if ($mmTable !== null) {
                $columns[$field] = (string)$mmTable;
            }
        }
        return $columns;
    }

    /**
     * @return int[]
     */
    private function getRelationUids(string $mmTable, int $uid): array
    {
        $queryBuilder = $this->connectionPool->getConnectionForTable($mmTable)->createQueryBuilder();
        $uids = $queryBuilder
            ->select('uid_foreign')
            ->from($mmTable)
            ->where(
                $queryBuilder->expr()->eq('uid_local', $queryBuilder->createNamedParameter($uid, ParameterType::INTEGER))
            )
            ->orderBy('sorting')
            ->executeQuery()
            ->fetchFirstColumn();

        return array_map('intval', $uids);
    }

    /**
     * Resolves foreign uids to ['uid' => int, 'label' => string] entries,
     * preserving the given order. The label field is taken from the foreign
     * table's TCA ctrl definition.
     *
     * @param int[] $uids
     * @return array<int, array{uid: int, label: string}>
     */
    private function resolveLabels(string $foreignTable, array $uids): array
    {
        if ($foreignTable === '' || $uids === []) {
            return [];
        }

        $labelField = (string)($GLOBALS['TCA'][$foreignTable]['ctrl']['label'] ?? 'uid');

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($foreignTable);
        $rows = $queryBuilder
            ->select('uid', $labelField)
            ->from($foreignTable)
            ->where(
                $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($uids, ArrayParameterType::INTEGER))
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $labels = [];
        foreach ($rows as $row) {
            $labels[(int)$row['uid']] = (string)$row[$labelField];
        }

        $result = [];
        foreach ($uids as $uid) {
            if (isset($labels[$uid])) {
                $result[] = ['uid' => $uid, 'label' => $labels[$uid]];
            }
        }
        return $result;
    }

    private function getDataHandler(): DataHandler
    {
        return GeneralUtility::makeInstance(DataHandler::class);
    }
}
