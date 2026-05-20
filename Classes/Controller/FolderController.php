<?php

namespace Fixpunkt\FpFileprotector\Controller;

use Fixpunkt\FpFileprotector\Domain\Repository\FolderRepository;
use Fixpunkt\FpFileprotector\Domain\Repository\ProtectionRepository;
use Fixpunkt\FpFileprotector\Utility\FolderUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class FolderController extends ActionController {
    /** @var ProtectionRepository  */
    protected ProtectionRepository $protectionRepository;
    /** @var FolderRepository  */
    protected FolderRepository $folderRepository;

    /**
     * @param ProtectionRepository $protectionRepository
     * @param FolderRepository $folderRepository
     */
    public function __construct(ProtectionRepository $protectionRepository, FolderRepository $folderRepository) {
        $this -> protectionRepository = $protectionRepository;
        $this -> folderRepository = $folderRepository;
    }

    /**
     * Gibt Informationen für einen einzelnen Ordner aus.
     * @param string $combinedIdentifier
     * @return void
     */
    public function showAction(string $combinedIdentifier) : void {
        $this -> view -> assignMultiple([
            'folder' => $this -> folderRepository -> findOneByCombinedIdentifier($combinedIdentifier)
        ]);
    }

}