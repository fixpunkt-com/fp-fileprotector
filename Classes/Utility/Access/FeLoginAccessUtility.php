<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Utility\Access;

use Fixpunkt\FpFileprotector\Domain\Model\Protection;
use Fixpunkt\FpFileprotector\Utility\FrontendUserUtility;

class FeLoginAccessUtility implements AccessUtilityInterface
{
    public function __construct(private readonly FrontendUserUtility $frontendUserUtility) {}

    public function isGranted(Protection $protection): bool
    {
        if (!$protection->isFeLogin()) {
            return false;
        }

        $feUser = $this->frontendUserUtility->getCurrentFrontendUser();
        if (!$feUser || !$feUser->isLoggedIn()) {
            return false;
        }

        if ($protection->getUserGroups()->count() === 0 && $protection->getUsers()->count() === 0) {
            return true;
        }

        if (in_array($feUser->get('id'), $protection->getUsersUids(), true)) {
            return true;
        }

        foreach ($feUser->get('groupIds') as $userGroupId) {
            if (in_array($userGroupId, $protection->getUserGroupsUids(), true)) {
                return true;
            }
        }

        return false;
    }

    public function getPartial(): string
    {
        return 'Access/Fe';
    }
}
