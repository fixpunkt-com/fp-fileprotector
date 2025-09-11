<?php

namespace Fixpunkt\FpFileprotector\Utility;

use Fixpunkt\FpFileprotector\Domain\Model\FrontendUser;
use Fixpunkt\FpFileprotector\Domain\Model\FrontendUserGroup;
use Fixpunkt\FpFileprotector\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Session\UserSessionManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class FrontendUserUtility {

    /**
     * Ermittelt den aktuellen Frontend User.
     * @return FrontendUser|null
     */
    public function getCurrentFrontendUser() : ?UserAspect {
        try {
            /** @var Context $context */
            $context = GeneralUtility::makeInstance(Context::class);
            /** @var UserAspect $userAspect */
            return $context -> getAspect("frontend.user");
        } catch(\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the usergroups or all usergroups (recursivly) of a frontend user.
     * @param FrontendUser $frontendUser
     * @return ObjectStorage
     */
    public function getUsergroups(FrontendUser $frontendUser) : ObjectStorage {
        DebuggerUtility::var_dump($frontendUser);
        die();
        // Benutzergruppen ermitteln
        $usergroupsToProcess = $frontendUser -> getUserGroups() -> toArray();
        $usergroups = new ObjectStorage();

        /** @var FrontendUserGroup $usergroup */
        while($usergroup = array_pop($usergroupsToProcess)) {
            if(!$usergroups -> contains($usergroup)) {
                $usergroupsToProcess = array_merge($usergroupsToProcess, $usergroup -> getSubgroups() -> toArray());
                $usergroups -> attach($usergroup);
            }
        }

        return $usergroups;
    }
}