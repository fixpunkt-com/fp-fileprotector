<?php

namespace Fixpunkt\FpFileprotector\Resource;

use Fixpunkt\FpFileprotector\Utility\HtaccessUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource as Core;
use TYPO3\CMS\Core\Resource\Driver\DriverInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Diese Klasse ist NICHT in TCA definiert sondern dient nur zum einfacheren Arbeiten mit den Storages.
 */
class ResourceStorage extends Core\ResourceStorage {
    /**
     * @return bool
     */
    public function isProtected() : bool {
        return $this -> storageRecord["protected"];
    }
    /**
     * @param bool $protected
     * @return void
     */
    public function setProtected(bool $protected) : void {
        $this -> storageRecord["protected"] = $protected;
    }

    /**
     * @return bool
     */
    public function isProtectedByDefault() : bool {
        return $this -> storageRecord["protected_by_default"];
    }
    /**
     * @param bool $protectedByDefault
     * @return void
     */
    public function setProtectedByDefault(bool $protectedByDefault) : void {
        $this -> storageRecord["protected_by_default"] = $protectedByDefault;
    }

    /**
     * @return bool
     */
    public function hasHtaccess() : bool {
        /** @var HtaccessUtility $htaccessUtility */
        $htaccessUtility = GeneralUtility::makeInstance(HtaccessUtility::class, $this);
        return $htaccessUtility -> hasHtaccess();
    }

    /**
     * Passt eine .htaccess-Datei an.
     * @return void
     */
    public function modifyHtaccess() : void {
        /** @var HtaccessUtility $htaccessUtility */
        $htaccessUtility = GeneralUtility::makeInstance(HtaccessUtility::class, $this);

        if($this -> isProtected()) {
            $htaccessUtility -> addHtaccess();
        } else {
            $htaccessUtility -> removeHtaccess();
        }
    }

}