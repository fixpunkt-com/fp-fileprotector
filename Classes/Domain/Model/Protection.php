<?php

namespace Fixpunkt\FpFileprotector\Domain\Model;

use Fixpunkt\FpFileprotector\Domain\Repository\FolderRepository;
use Fixpunkt\FpFileprotector\Resource\Folder;
use Fixpunkt\FpFileprotector\Utility\FrontendUserUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Protection extends AbstractEntity {
    /** @var int  */
    protected int $storage = 0;
    /** @var string  */
    protected string $folder = "";
    /** @var bool  */
    protected bool $feLogin = false;
    /** @var bool  */
    protected bool $beLogin = false;
    /** @var ObjectStorage<FrontendUserGroup>|null  */
    protected ?ObjectStorage $userGroups = null;
    /** @var ObjectStorage<FrontendUser>|null  */
    protected ?ObjectStorage $users = null;

    /**
     *
     */
    public function __construct() {
        $this -> userGroups = new ObjectStorage();
        $this -> users = new ObjectStorage();
    }

    /**
     * @return int
     */
    public function getStorage() : int {
        return $this->storage;
    }
    /**
     * @param int $storage
     */
    public function setStorage(int $storage) : void{
        $this->storage = $storage;
    }

    /**
     * @return string
     */
    public function getFolder() : string {
        return $this -> folder;
    }
    /**
     * @param string $folder
     */
    public function setFolder(string $folder): void {
        $this->folder = $folder;
    }
    /**
     * Gibt den Ordner als Objekt zurück.
     * @return Folder|null
     */
    public function getFolderObject() : ?Folder {
        /** @var FolderRepository $folderRepository */
        $folderRepository = GeneralUtility::makeInstance(FolderRepository::class);
        return $folderRepository -> findOneByCombinedIdentifier($this -> getStorage().":".$this -> getFolder());
    }

    /**
     * @return bool
     */
    public function isFeLogin() : bool {
        return $this->feLogin;
    }
    /**
     * @param bool $feLogin
     */
    public function setFeLogin(bool $feLogin) : void {
        $this->feLogin = $feLogin;
    }

    /**
     * @return bool
     */
    public function isBeLogin() : bool {
        return $this->beLogin;
    }
    /**
     * @param bool $beLogin
     */
    public function setBeLogin(bool $beLogin) : void {
        $this->beLogin = $beLogin;
    }

    /**
     * @return ObjectStorage<FrontendUserGroup>|null
     */
    public function getUserGroups() : ?ObjectStorage {
        return $this->userGroups;
    }
    /**
     * @param ObjectStorage<FrontendUserGroup> $userGroups
     */
    public function setUserGroups(ObjectStorage $userGroups) : void {
        $this->userGroups = $userGroups;
    }
    /**
     * @param FrontendUserGroup $userGroup
     * @return void
     */
    public function addUserGroup(FrontendUserGroup $userGroup) : void {
        $this -> userGroups -> attach($userGroup);
    }
    /**
     * @param FrontendUserGroup $userGroup
     * @return void
     */
    public function removeUserGroup(FrontendUserGroup $userGroup) : void {
        $this -> userGroups -> detach($userGroup);
    }

    /**
     * @return ObjectStorage<FrontendUser>|null
     */
    public function getUsers() : ?ObjectStorage {
        return $this->users;
    }
    /**
     * @param ObjectStorage<FrontendUser> $users
     */
    public function setUsers(ObjectStorage $users): void {
        $this->users = $users;
    }
    /**
     * @param FrontendUser $user
     * @return void
     */
    public function addUser(FrontendUser $user) : void {
        $this -> users -> attach($user);
    }
    /**
     * @param FrontendUser $user
     * @return void
     */
    public function removeUser(FrontendUser $user) : void {
        $this -> users -> detach($user);
    }

    /**
     * Prüft ob die Person, welche auf die Resource zugreifen will, dazu berechtigt ist.
     * @return bool
     */
    public function isGranted() : bool {
        if($this -> isFeLogin()) {
            // FE-Benutzer:in muss in Benutzer:innen-Gruppe sein oder wurde speziell ausgewählt
            $frontendUserUtility = GeneralUtility::makeInstance(FrontendUserUtility::class);
            $feUser = $frontendUserUtility -> getCurrentFrontendUser();
            if($feUser) {
                if($this -> getUserGroups() -> count() == 0 && $this -> getUsers() -> count() == 0) {
                    // Es reicht wenn man eingeloggt ist, keine weiteren Einschränkungen
                    return true;
                }

                // Benutzer überprüfen
                if($this -> getUsers() -> contains($feUser)) {
                    return true;
                }

                // Benutzergruppen überprüfen
                foreach($frontendUserUtility -> getUsergroups($feUser) as $userGroup) {
                    if($this -> getUserGroups() -> contains($userGroup)) {
                        return true;
                    }
                }
            }
        }
        if($this -> isBeLogin() && $GLOBALS['BE_USER']) {
            return true;
        }

        return false;
    }

    /**
     * Gibt an, ob der Verzeichnisschutz überhaupt schützt.
     * @return bool
     */
    public function isProtected() : bool {
        return $this -> isFeLogin() || $this -> isBeLogin();
    }

}