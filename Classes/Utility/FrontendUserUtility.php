<?php

namespace Fixpunkt\FpFileprotector\Utility;

use Fixpunkt\FpFileprotector\Resource\ResourceStorage;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
}