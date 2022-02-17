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
     * @return ResourceStorage
     */
    public function getStorage() : ResourceStorage {
        return parent::getStorage();
    }

    /**
     * Prüft, ob es sich bei dem Ordner um den RootLevelFolder handelt.
     * @return bool
     */
    public function isRootLevelFolder() : bool {
        return $this -> getStorage() -> getRootLevelFolder() -> getIdentifier() == $this -> getIdentifier();
    }

    /**
     * Gibt den übergeordneten Ordner zurück, wenn er existiert.
     * @return Folder|null
     */
    public function getParentFolder() : ?Folder {
        if($this -> isRootLevelFolder()) {
            return null;
        }
        return parent::getParentFolder();
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
        return $this -> getProtection() && $this -> getStorage() -> isProtectedByDefault() || !$this -> getStorage() -> isProtectedByDefault();
    }

    /**
     * Gibt die Rootline des Ordners zurück.
     * @return array
     */
    public function getRootline() : array {
        if($this -> getParentFolder()) {
            $childFolders = $this -> getParentFolder() -> getRootline();
            $childFolders[] = $this;
            return $childFolders;
        } else {
            return [$this];
        }
    }
}