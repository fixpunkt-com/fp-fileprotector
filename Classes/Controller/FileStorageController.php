<?php

namespace Fixpunkt\FpFileprotector\Controller;

use Fixpunkt\FpFileprotector\Domain\Repository\FileStorageRepository;
use Fixpunkt\FpFileprotector\Domain\Repository\ProtectionRepository;
use Fixpunkt\FpFileprotector\Resource\ResourceStorage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class FileStorageController extends ActionController {
    /** @var ProtectionRepository  */
    protected ProtectionRepository $protectionRepository;
    /** @var FileStorageRepository  */
    protected FileStorageRepository $fileStorageRepository;

    /**
     * @param ProtectionRepository $protectionRepository
     * @param FileStorageRepository $fileStorageRepository
     */
    public function __construct(ProtectionRepository $protectionRepository, FileStorageRepository $fileStorageRepository) {
        $this -> protectionRepository = $protectionRepository;
        $this -> fileStorageRepository = $fileStorageRepository;
    }

    /**
     * Listet alle Storages auf.
     * @return void
     * @throws StopActionException
     */
    public function listAction() : void {
        $this -> view -> assignMultiple([
            'fileStorages' => $this -> fileStorageRepository -> findAll()
        ]);
    }

    /**
     * Stellt eine Maske zum Bearbeiten des FileStorages zur Verfügung.
     * @param int $fileStorageUid
     * @return void
     */
    public function editAction(int $fileStorageUid) : void {
        /** @var ResourceStorage $fileStorage */
        $this -> view -> assign('fileStorage', $this -> fileStorageRepository -> findByIdentifier($fileStorageUid));
    }

    public function updateAction(int $fileStorageUid, bool $protected, bool $protectedByDefault) : void {
        $fileStorage = $this -> fileStorageRepository -> findByIdentifier($fileStorageUid);
        $fileStorage -> setProtected($protected);
        $fileStorage -> setProtectedByDefault($protectedByDefault);
        $fileStorage -> modifyHtaccess();
        $this -> fileStorageRepository -> update($fileStorage);

        $this -> addFlashMessage("Der FileStorage wurde angepasst");
        $this -> redirect('list');
    }

    /**
     * Passt die .htaccess-Datei an.
     * @param int $fileStorageUid
     * @return void
     * @throws StopActionException
     */
    public function htaccessAction(int $fileStorageUid) : void {
        $this -> fileStorageRepository -> findByIdentifier($fileStorageUid) -> modifyHtaccess();

        $this -> addFlashMessage("Die .htaccess-Datei wurde angepasst.");
        $this -> redirect('list');
    }

    /**
     * Zeigt eine Gesamtübersicht über alle Ordner eines Storages an.
     * @param int $fileStorageUid
     * @return void
     */
    public function showAction(int $fileStorageUid) : void {
        $this -> view -> assign('fileStorage', $this -> fileStorageRepository -> findByIdentifier($fileStorageUid));
    }

}