<?php

namespace Fixpunkt\FpFileprotector\Utility;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
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

            /** @var ObjectManager $objectManager */
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            /** @var FrontendUserRepository $frontendUserRepository */
            $frontendUserRepository = $objectManager -> get(FrontendUserRepository::class);
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
                $usergroupsToProcess = array_merge($usergroupsToProcess, $usergroup -> getSubgroup() -> toArray());
                $usergroups -> attach($usergroup);
            }
        }

        return $usergroups;
    }
}