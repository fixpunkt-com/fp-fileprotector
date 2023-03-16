<?php
defined('TYPO3_MODE') || die('Access denied.');

// StorageRecord erweitern
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Resource\ResourceStorage::class] = [
    'className' => \Fixpunkt\FpFileprotector\Resource\ResourceStorage::class
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Resource\Folder::class] = [
    'className' => \Fixpunkt\FpFileprotector\Resource\Folder::class
];