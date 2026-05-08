<?php

namespace Fixpunkt\FpFileprotector\Controller;

use Fixpunkt\FpFileprotector\Domain\Model\Protection;
use Fixpunkt\FpFileprotector\Domain\Repository\FolderRepository;
use Fixpunkt\FpFileprotector\Domain\Repository\FrontendUserGroupRepository;
use Fixpunkt\FpFileprotector\Domain\Repository\FrontendUserRepository;
use Fixpunkt\FpFileprotector\Domain\Repository\ProtectionRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Crypto\HashService;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Controller\MvcPropertyMappingConfigurationService;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\View\ViewResolverInterface;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Property\PropertyMapper;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Extbase\Service\FileHandlingService;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;
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

    /** @var ModuleTemplateFactory  */
    protected ModuleTemplateFactory $moduleTemplateFactory;

    public function __construct(
        ModuleTemplateFactory $moduleTemplateFactory,

        ProtectionRepository $protectionRepository,
        FrontendUserGroupRepository $userGroupRepository,
        FrontendUserRepository $userRepository,
        FolderRepository $folderRepository,
    ) {
        $this -> protectionRepository = $protectionRepository;
        $this -> userGroupRepository = $userGroupRepository;
        $this -> userRepository = $userRepository;
        $this -> folderRepository = $folderRepository;

        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings -> setRespectStoragePage(false);
        $this -> userGroupRepository -> setDefaultQuerySettings($querySettings);
        $this -> userRepository -> setDefaultQuerySettings($querySettings);

        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    /**
     * Stellt eine Oberfläche zum Erstellen eines Ordnerschutzes bereit.
     * @return void
     */
    public function newAction(string $combinedIdentifier) : \Psr\Http\Message\ResponseInterface {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        $moduleTemplate -> assignMultiple([
            'protection' => GeneralUtility::makeInstance(Protection::class),
            'folder' => $this -> folderRepository -> findOneByCombinedIdentifier($combinedIdentifier),
            'userGroups' => $this -> userGroupRepository -> findAll(),
            'users' => $this -> userRepository -> findAll(),
        ]);

        return $moduleTemplate->renderResponse("Protection/New");
    }

    /**
     * Legt einen neuen Ordnerschutz an.
     * @param Protection $protection
     * @return \Psr\Http\Message\ResponseInterface
     * @throws StopActionException
     * @throws IllegalObjectTypeException
     */
    public function createAction(Protection $protection) {
        $this -> protectionRepository -> add($protection);
        $this -> addFlashMessage(LocalizationUtility::translate(
            'tx_fpfileprotector_domain_model_protection.flashmessages.created',
            'FpFileprotector'
        ));
        return $this -> redirect('show','Folder', null, ['combinedIdentifier' => $protection -> getFolderObject() -> getCombinedIdentifier()]);
    }

    /**
     * Stellt eine Oberfläche zum Bearbeiten eines Ordnerschutzes bereit.
     * @param Protection $protection
     * @return void
     */
    public function editAction(Protection $protection) : \Psr\Http\Message\ResponseInterface {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        $moduleTemplate -> assignMultiple([
            'protection' => $protection,
            'folder' => $protection -> getFolderObject(),
            'userGroups' => $this -> userGroupRepository -> findAll(),
            'users' => $this -> userRepository -> findAll()
        ]);

        return $moduleTemplate->renderResponse("Protection/Edit");
    }

    /**
     * @param Protection $protection
     * @return \Psr\Http\Message\ResponseInterface
     * @throws StopActionException
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function updateAction(Protection $protection) {
        $this -> protectionRepository -> update($protection);
        $this -> addFlashMessage(LocalizationUtility::translate(
            'tx_fpfileprotector_domain_model_protection.flashmessages.updated',
            'FpFileprotector'
        ));
        return $this -> redirect('show','Folder', null, ['combinedIdentifier' => $protection -> getFolderObject() -> getCombinedIdentifier()]);
    }

    /**
     * Entfernt einen Ordnerschutz.
     * @param Protection $protection
     * @return \Psr\Http\Message\ResponseInterface
     * @throws StopActionException
     * @throws IllegalObjectTypeException
     */
    public function deleteAction(Protection $protection) {
        $this -> protectionRepository -> remove($protection);#
        $this -> addFlashMessage(LocalizationUtility::translate(
            'tx_fpfileprotector_domain_model_protection.flashmessages.deleted',
            'FpFileprotector'
        ));
        return $this -> redirect('show','Folder', null, ['combinedIdentifier' => $protection -> getFolderObject() -> getCombinedIdentifier()]);
    }

}