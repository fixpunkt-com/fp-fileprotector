<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Utility\Access;

use Doctrine\DBAL\ParameterType;
use Fixpunkt\FpFileprotector\Utility\FrontendUserUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

class FeLoginAccessUtility implements AccessUtilityInterface
{
    private const USERS_MM = 'tx_fpfileprotector_protection_feusers_mm';
    private const GROUPS_MM = 'tx_fpfileprotector_protection_fegroups_mm';

    public function __construct(
        private readonly FrontendUserUtility $frontendUserUtility,
        private readonly ConnectionPool $connectionPool,
    ) {}

    /** @param array<string, mixed> $protection Raw protection database record */
    public function isGranted(array $protection): bool
    {
        if (!(bool)$protection['fe_login']) {
            return false;
        }

        $feUser = $this->frontendUserUtility->getCurrentFrontendUser();
        if (!$feUser || !$feUser->isLoggedIn()) {
            return false;
        }

        $protectionUid = (int)$protection['uid'];
        $userUids = $this->getRelatedUids(self::USERS_MM, $protectionUid);
        $userGroupUids = $this->getRelatedUids(self::GROUPS_MM, $protectionUid);

        if ($userGroupUids === [] && $userUids === []) {
            return true;
        }

        if (in_array($feUser->get('id'), $userUids, true)) {
            return true;
        }

        foreach ($feUser->get('groupIds') as $userGroupId) {
            if (in_array($userGroupId, $userGroupUids, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Reads the related foreign uids from an MM table for a protection record.
     *
     * @return int[]
     */
    private function getRelatedUids(string $mmTable, int $protectionUid): array
    {
        $queryBuilder = $this->connectionPool->getConnectionForTable($mmTable)->createQueryBuilder();
        $uids = $queryBuilder
            ->select('uid_foreign')
            ->from($mmTable)
            ->where(
                $queryBuilder->expr()->eq('uid_local', $queryBuilder->createNamedParameter($protectionUid, ParameterType::INTEGER))
            )
            ->executeQuery()
            ->fetchFirstColumn();

        return array_map('intval', $uids);
    }

    public function getPartials(): string
    {
        return 'Access/Fe';
    }
}
