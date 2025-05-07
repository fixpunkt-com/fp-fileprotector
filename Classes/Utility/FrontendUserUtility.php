<?php

namespace Fixpunkt\FpFileprotector\Utility;

use Fixpunkt\FpFileprotector\Domain\Model\FrontendUser;
use Fixpunkt\FpFileprotector\Domain\Model\FrontendUserGroup;
use Fixpunkt\FpFileprotector\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class FrontendUserUtility {

    /**
     * Ermittelt den aktuellen Frontend User.
     * @return FrontendUser|null
     */
    public function getCurrentFrontendUser() : ?FrontendUser {
        try {
            /** @var Context $context */
            $context = GeneralUtility::makeInstance(Context::class);
            $userUid = $context->getPropertyFromAspect('frontend.user', 'id');

            /** @var FrontendUserRepository $frontendUserRepository */
            $frontendUserRepository = GeneralUtility::makeInstance(FrontendUserRepository::class);
            return $frontendUserRepository -> findByIdentifier($userUid);
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
        // Benutzergruppen ermitteln
        $usergroupsToProcess = $frontendUser -> getUsergroup() -> toArray();
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