<?php

namespace Fixpunkt\FpFileprotector\Controller;

use Fixpunkt\FpFileprotector\Domain\Repository\FolderRepository;
use Fixpunkt\FpFileprotector\Domain\Repository\ProtectionRepository;
use Fixpunkt\FpFileprotector\Utility\FolderUtility;
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
use TYPO3\CMS\Extbase\Mvc\View\ViewResolverInterface;
use TYPO3\CMS\Extbase\Property\PropertyMapper;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Extbase\Service\FileHandlingService;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;

class FolderController extends ActionController {
    /** @var ProtectionRepository  */
    protected ProtectionRepository $protectionRepository;
    /** @var FolderRepository  */
    protected FolderRepository $folderRepository;

    /** @var ModuleTemplateFactory  */
    protected ModuleTemplateFactory $moduleTemplateFactory;

    public function __construct(
        ModuleTemplateFactory $moduleTemplateFactory,

        ProtectionRepository $protectionRepository,
        FolderRepository $folderRepository,

        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        ConfigurationManagerInterface $configurationManager,
        ValidatorResolver $validatorResolver,
        ViewResolverInterface $viewResolver,
        ViewFactoryInterface $viewFactory,
        ReflectionService $reflectionService,
        HashService $hashService,
        MvcPropertyMappingConfigurationService $mvcPropertyMappingConfigurationService,
        EventDispatcherInterface $eventDispatcher,
        FileHandlingService $fileHandlingService,
        PropertyMapper $propertyMapper,
        FlashMessageService $flashMessageService,
        ExtensionService $extensionService
    ) {
        $this -> protectionRepository = $protectionRepository;
        $this -> folderRepository = $folderRepository;


        // Explizit die inject-Methoden aufrufen, da Symfony das nicht mehr tut
        $this->injectResponseFactory($responseFactory);
        $this->injectStreamFactory($streamFactory);
        $this->injectConfigurationManager($configurationManager);
        $this->injectValidatorResolver($validatorResolver);
        $this->injectViewResolver($viewResolver);
        $this->injectViewFactory($viewFactory);
        $this->injectReflectionService($reflectionService);
        $this->injectHashService($hashService);
        $this->injectMvcPropertyMappingConfigurationService($mvcPropertyMappingConfigurationService);
        $this->injectEventDispatcher($eventDispatcher);
        $this->injectFileHandlingService($fileHandlingService);
        $this->injectPropertyMapper($propertyMapper);
        $this->injectInternalFlashMessageService($flashMessageService);
        $this->injectInternalExtensionService($extensionService);

        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    /**
     * Gibt Informationen für einen einzelnen Ordner aus.
     * @param string $combinedIdentifier
     * @return void
     */
    public function showAction(string $combinedIdentifier) : \Psr\Http\Message\ResponseInterface {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        $moduleTemplate -> assignMultiple([
            'folder' => $this -> folderRepository -> findOneByCombinedIdentifier($combinedIdentifier)
        ]);
        return $moduleTemplate->renderResponse("Folder/Show");
    }

}