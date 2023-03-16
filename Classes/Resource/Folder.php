<?php

namespace Fixpunkt\FpFileprotector\Resource;

use Fixpunkt\FpFileprotector\Domain\Model\Protection;
use Fixpunkt\FpFileprotector\Domain\Repository\ProtectionRepository;
use Fixpunkt\FpFileprotector\Utility\HtaccessUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource as Core;
use TYPO3\CMS\Core\Resource\Driver\DriverInterface;
use TYPO3\CMS\Core\Resource\FolderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Diese Klasse ist NICHT in TCA definiert sondern dient nur zum einfacheren Arbeiten mit den Storages.
 */
class Folder extends Core\Folder {
    // Override functions
    /**
     * @return ResourceStorage
     */
    public function getStorage() : ResourceStorage {
        return parent::getStorage();
    }

    // New functions
    /**
     * Gibt den Protection dieses Ordners oder seine übergeordneten Ordner zurück.
     * @return Protection|null
     */
    public function getProtection() : ?Protection {
        /** @var ProtectionRepository $protectionRepository */
        $protectionRepository = GeneralUtility::makeInstance(ProtectionRepository::class);
        return $protectionRepository -> getProtection($this);
    }
    /**
     * Gibt den Protection genau dieses Ordners zurück.
     * @return Protection|null
     */
    public function getOwnProtection() : ?Protection {
        /** @var ProtectionRepository $protectionRepository */
        $protectionRepository = GeneralUtility::makeInstance(ProtectionRepository::class);
        return $protectionRepository -> findOneByFolder($this);
    }

    /**
     * Prüft, ob es sich bei dem Ordner um den RootLevelFolder handelt.
     * @return bool
     */
    public function isRootLevelFolder() : bool {
        return $this -> getStorage() -> getRootLevelFolder() -> getIdentifier() == $this -> getIdentifier();
    }

    /**
     * @return bool
     */
    public function hasParentFolder() : bool {
        return !$this -> isRootLevelFolder();
    }

    /**
     * Gibt zurück, ob ein Zugriffsschutz besteht.
     * @return bool
     */
    public function isProtected() : bool {
        return $this -> getProtection() ? $this -> getProtection() -> isProtected() : false;
    }

    /**
     * Gibt an, ob auf den Ordner zugegriffen werden kann oder nicht.
     * @return bool
     */
    public function isAccessible() : bool {
        return
            $this -> getProtection() && $this -> getStorage() -> isProtectedByDefault() && $this -> isProtected() ||
            !$this -> getStorage() -> isProtectedByDefault();
    }

    /**
     * Gibt die Rootline des Ordners zurück.
     * @return array
     */
    public function getRootline() : array {
        if($this -> hasParentFolder()) {
            $childFolders = $this -> getParentFolder() -> getRootline();
            $childFolders[] = $this;
            return $childFolders;
        } else {
            return [$this];
        }
    }
}