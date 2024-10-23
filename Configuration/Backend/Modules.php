<?php

return [
    'file_FpFileprotectorProtection' => [
        'parent' => 'file',
        'access' => 'user',
        'iconIdentifier' => null,
        'labels' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang_module_protection.xlf',
        'extensionName' => 'FpFileprotector',
        'controllerActions' => [
            'Fixpunkt\FpFileprotector\Controller\StartController' => [
                'start',
            ],
            'Fixpunkt\FpFileprotector\Controller\FileStorageController' => [
                'list',
                'htaccess',
                'edit',
                'update',
                'show',
            ],
            'Fixpunkt\FpFileprotector\Controller\FolderController' => [
                'show',
            ],
            'Fixpunkt\FpFileprotector\Controller\ProtectionController' => [
                'new',
                'create',
                'edit',
                'update',
                'delete',
            ],
        ],
    ],
];
