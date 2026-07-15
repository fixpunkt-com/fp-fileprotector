<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Controller;

use Fixpunkt\FpFileprotector\Domain\Repository\FolderRepository;
use Fixpunkt\FpFileprotector\Domain\Repository\FrontendUserGroupRepository;
use Fixpunkt\FpFileprotector\Domain\Repository\FrontendUserRepository;
use Fixpunkt\FpFileprotector\Service\AccessService;
use Fixpunkt\FpFileprotector\Service\ProtectionService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

#[AsController]
class ProtectionController extends ActionController
{
    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        protected readonly FrontendUserGroupRepository $userGroupRepository,
        protected readonly FrontendUserRepository $userRepository,
        protected readonly FolderRepository $folderRepository,
        protected readonly AccessService $accessService,
        protected readonly ProtectionService $protectionService,
    ) {
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->userGroupRepository->setDefaultQuerySettings($querySettings);
        $this->userRepository->setDefaultQuerySettings($querySettings);
    }

    /**
     * Provides the folder protection creation form.
     */
    public function newAction(string $combinedIdentifier): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        $moduleTemplate->assignMultiple([
            'folder' => $this->folderRepository->findOneByCombinedIdentifier($combinedIdentifier),
            'userGroups' => $this->userGroupRepository->findAll(),
            'users' => $this->userRepository->findAll(),
            'accessPartials' => $this->accessService->getPartials(),
            'record' => [],
        ]);

        return $moduleTemplate->renderResponse('Protection/New');
    }

    /**
     * Creates a new folder protection.
     *
     * @param array<string, mixed> $protection Raw access-type fields keyed by TCA column
     */
    public function createAction(string $combinedIdentifier, array $protection = []): ResponseInterface
    {
        $folder = $this->folderRepository->findOneByCombinedIdentifier($combinedIdentifier);
        $this->protectionService->create(
            $folder->getStorage()->getUid(),
            $folder->getIdentifier(),
            $protection,
            $this->getStoragePid()
        );

        $this->addFlashMessage(LocalizationUtility::translate(
            'tx_fpfileprotector_domain_model_protection.flashmessages.created',
            'FpFileprotector'
        ));
        return $this->redirect('show', 'Folder', null, ['id' => $combinedIdentifier, 'refreshFolderTree' => true]);
    }

    /**
     * Provides the folder protection edit form.
     */
    public function editAction(int $protectionUid, string $combinedIdentifier): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        $moduleTemplate->assignMultiple([
            'folder' => $this->folderRepository->findOneByCombinedIdentifier($combinedIdentifier),
            'userGroups' => $this->userGroupRepository->findAll(),
            'users' => $this->userRepository->findAll(),
            'accessPartials' => $this->accessService->getPartials(),
            'protectionUid' => $protectionUid,
            'record' => $this->protectionService->getFormValues($protectionUid),
        ]);

        return $moduleTemplate->renderResponse('Protection/Edit');
    }

    /**
     * Updates an existing folder protection.
     *
     * @param array<string, mixed> $protection Raw access-type fields keyed by TCA column
     */
    public function updateAction(int $protectionUid, string $combinedIdentifier, array $protection = []): ResponseInterface
    {
        $this->protectionService->update($protectionUid, $protection);

        $this->addFlashMessage(LocalizationUtility::translate(
            'tx_fpfileprotector_domain_model_protection.flashmessages.updated',
            'FpFileprotector'
        ));
        return $this->redirect('show', 'Folder', null, ['id' => $combinedIdentifier, 'refreshFolderTree' => true]);
    }

    /**
     * Removes folder protection.
     */
    public function deleteAction(int $protectionUid, string $combinedIdentifier): ResponseInterface
    {
        $this->protectionService->delete($protectionUid);

        $this->addFlashMessage(LocalizationUtility::translate(
            'tx_fpfileprotector_domain_model_protection.flashmessages.deleted',
            'FpFileprotector'
        ));
        return $this->redirect('show', 'Folder', null, ['id' => $combinedIdentifier, 'refreshFolderTree' => true]);
    }

    /**
     * Returns the configured storage pid for new records (falls back to root).
     */
    protected function getStoragePid(): int
    {
        $framework = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
        return (int)($framework['persistence']['storagePid'] ?? 0);
    }
}
