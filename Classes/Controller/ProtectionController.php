<?php

namespace Fixpunkt\FpFileprotector\Controller;

use Fixpunkt\FpFileprotector\Domain\Model\Protection;
use Fixpunkt\FpFileprotector\Domain\Repository\FolderRepository;
use Fixpunkt\FpFileprotector\Domain\Repository\ProtectionRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Form\Service\TranslationService;

class ProtectionController extends ActionController {
    /** @var ProtectionRepository  */
    protected ProtectionRepository $protectionRepository;
    /** @var FrontendUserGroupRepository  */
    protected FrontendUserGroupRepository $userGroupRepository;
    /** @var FrontendUserRepository  */
    protected FrontendUserRepository $userRepository;
    /** @var FolderRepository  */
    protected FolderRepository $folderRepository;

    /**
     * @param ProtectionRepository $protectionRepository
     * @param FrontendUserGroupRepository $userGroupRepository
     * @param FrontendUserRepository $userRepository
     * @param FolderRepository $folderRepository
     */
    public function __construct(ProtectionRepository $protectionRepository, FrontendUserGroupRepository $userGroupRepository, FrontendUserRepository $userRepository, FolderRepository $folderRepository) {
        $this -> protectionRepository = $protectionRepository;
        $this -> userGroupRepository = $userGroupRepository;
        $this -> userRepository = $userRepository;
        $this -> folderRepository = $folderRepository;

        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings -> setRespectStoragePage(false);
        $this -> userGroupRepository -> setDefaultQuerySettings($querySettings);
        $this -> userRepository -> setDefaultQuerySettings($querySettings);
    }

    /**
     * Stellt eine Oberfläche zum Erstellen eines Ordnerschutzes bereit.
     * @return void
     */
    public function newAction(string $combinedIdentifier) : void {
        $this -> view -> assignMultiple([
            'folder' => $this -> folderRepository -> findOneByCombinedIdentifier($combinedIdentifier),
            'userGroups' => $this -> userGroupRepository -> findAll(),
            'users' => $this -> userRepository -> findAll(),
        ]);
    }

    /**
     * Legt einen neuen Ordnerschutz an.
     * @param Protection $protection
     * @return void
     * @throws StopActionException
     * @throws IllegalObjectTypeException
     */
    public function createAction(Protection $protection) : void {
        $this -> protectionRepository -> add($protection);
        $this -> addFlashMessage(LocalizationUtility::translate(
            'tx_fpfileprotector_domain_model_protection.flashmessages.created',
            'FpFileprotector'
        ));
        $this -> redirect('show','Folder', null, ['combinedIdentifier' => $protection -> getFolderObject() -> getCombinedIdentifier()]);
    }

    /**
     * Stellt eine Oberfläche zum Bearbeiten eines Ordnerschutzes bereit.
     * @param Protection $protection
     * @return void
     */
    public function editAction(Protection $protection) : void {
        $this -> view -> assignMultiple([
            'protection' => $protection,
            'folder' => $protection -> getFolderObject(),
            'userGroups' => $this -> userGroupRepository -> findAll(),
            'users' => $this -> userRepository -> findAll()
        ]);
    }

    /**
     * @param Protection $protection
     * @return void
     * @throws StopActionException
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function updateAction(Protection $protection) : void {
        $this -> protectionRepository -> update($protection);
        $this -> addFlashMessage(LocalizationUtility::translate(
            'tx_fpfileprotector_domain_model_protection.flashmessages.updated',
            'FpFileprotector'
        ));
        $this -> redirect('show','Folder', null, ['combinedIdentifier' => $protection -> getFolderObject() -> getCombinedIdentifier()]);
    }

    /**
     * Entfernt einen Ordnerschutz.
     * @param Protection $protection
     * @return void
     * @throws StopActionException
     * @throws IllegalObjectTypeException
     */
    public function deleteAction(Protection $protection) : void {
        $this -> protectionRepository -> remove($protection);#
        $this -> addFlashMessage(LocalizationUtility::translate(
            'tx_fpfileprotector_domain_model_protection.flashmessages.deleted',
            'FpFileprotector'
        ));
        $this -> redirect('show','Folder', null, ['combinedIdentifier' => $protection -> getFolderObject() -> getCombinedIdentifier()]);
    }

}