<?php
defined('TYPO3_MODE') || die('Access denied.');

// Backend Modul
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'FpFileprotector',
    'file',
    'protection',
    '',
    [
        \Fixpunkt\FpFileprotector\Controller\StartController::class => 'start',
        \Fixpunkt\FpFileprotector\Controller\FileStorageController::class => 'list,htaccess,edit,update,show',
        \Fixpunkt\FpFileprotector\Controller\FolderController::class => 'show',
        \Fixpunkt\FpFileprotector\Controller\ProtectionController::class => 'new,create,edit,update,delete'
    ],
    [
        'access' => 'user,group',
        'icon' => 'EXT:fp_fileprotector/Resources/Public/Icons/Modules/protection.jpg',
        'labels' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang_module_protection.xlf',
    ]
);