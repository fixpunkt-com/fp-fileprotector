<?php

namespace Fixpunkt\FpFileprotector\Controller;

use Fixpunkt\FpFileprotector\Domain\Repository\FileStorageRepository;
use Fixpunkt\FpFileprotector\Domain\Repository\ProtectionRepository;
use Fixpunkt\FpFileprotector\Resource\ResourceStorage;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Crypto\HashService;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Controller\MvcPropertyMappingConfigurationService;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\View\ViewResolverInterface;
use TYPO3\CMS\Extbase\Property\PropertyMapper;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Extbase\Service\FileHandlingService;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;

class FileStorageController extends ActionController {
    /** @var ProtectionRepository  */
    protected ProtectionRepository $protectionRepository;
    /** @var FileStorageRepository  */
    protected FileStorageRepository $fileStorageRepository;

    /** @var ModuleTemplateFactory  */
    protected ModuleTemplateFactory $moduleTemplateFactory;

    public function __construct(
                                ModuleTemplateFactory $moduleTemplateFactory,

                                ProtectionRepository $protectionRepository,
                                FileStorageRepository $fileStorageRepository,
    ) {
        $this -> protectionRepository = $protectionRepository;
        $this -> fileStorageRepository = $fileStorageRepository;

        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    /**
     * Listet alle Storages auf.
     * @return void
     * @throws StopActionException
     */
    public function listAction() : \Psr\Http\Message\ResponseInterface {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        $moduleTemplate -> assignMultiple([
            'fileStorages' => $this -> fileStorageRepository -> findAll()
        ]);

        return $moduleTemplate->renderResponse("FileStorage/List");
    }

    /**
     * Stellt eine Maske zum Bearbeiten des FileStorages zur Verfügung.
     * @param int $fileStorageUid
     * @return void
     */
    public function editAction(int $fileStorageUid) : \Psr\Http\Message\ResponseInterface {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        /** @var ResourceStorage $fileStorage */
        $moduleTemplate -> assign('fileStorage', $this -> fileStorageRepository -> findByIdentifier($fileStorageUid));
        return $moduleTemplate->renderResponse("FileStorage/Edit");
    }

    public function updateAction(int $fileStorageUid, bool $protected, bool $protectedByDefault) {
        $fileStorage = $this -> fileStorageRepository -> findByIdentifier($fileStorageUid);
        $fileStorage -> setProtected($protected);
        $fileStorage -> setProtectedByDefault($protectedByDefault);
        $fileStorage -> modifyHtaccess();
        $this -> fileStorageRepository -> update($fileStorage);

        $this -> addFlashMessage("Der FileStorage wurde angepasst");
        return $this -> redirect('list');
    }

    /**
     * Passt die .htaccess-Datei an.
     * @param int $fileStorageUid
     * @return \Psr\Http\Message\ResponseInterface
     * @throws StopActionException
     */
    public function htaccessAction(int $fileStorageUid) {
        $this -> fileStorageRepository -> findByIdentifier($fileStorageUid) -> modifyHtaccess();

        $this -> addFlashMessage("Die .htaccess-Datei wurde angepasst.");
        return $this -> redirect('list');
    }

    /**
     * Zeigt eine Gesamtübersicht über alle Ordner eines Storages an.
     * @param int $fileStorageUid
     * @return void
     */
    public function showAction(int $fileStorageUid) : \Psr\Http\Message\ResponseInterface {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate -> assign('fileStorage', $this -> fileStorageRepository -> findByIdentifier($fileStorageUid));
        return $moduleTemplate->renderResponse("FileStorage/Show");
    }

}