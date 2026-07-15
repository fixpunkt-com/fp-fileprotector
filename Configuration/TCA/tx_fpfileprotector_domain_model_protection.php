<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection',
        'label' => 'folder',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'searchFields' => 'folder',
        // Protection rules are stored globally on the root level (pid=0), so no
        // storage page needs to be configured. rootLevel=1 permits records on
        // pid=0, and ignoreRootLevelRestriction lets non-admin backend users
        // (the module is available to "user") create and edit them there.
        'rootLevel' => 1,
        'security' => [
            'ignoreRootLevelRestriction' => true,
        ],
        'iconfile' => 'EXT:fp_fileprotector/Resources/Public/Icons/Models/tx_fpfileprotector_domain_model_protection.svg',
    ],
    'palettes' => [
        'folder' => [
            'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.palette.folder',
            'showitem' => 'storage,folder',
        ],
    ],
    'types' => [
        0 => ['showitem' => '--palette--;;folder'],
    ],
    'columns' => [
        'storage' => [
            'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.storage',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => '', 'value' => 0],
                ],
                'foreign_table' => 'sys_file_storage',
                'foreign_table_where' => 'AND {#sys_file_storage}.{#protected} = 1',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'default' => 0,
            ],
        ],
        'folder' => [
            'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.folder',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [],
                'itemsProcFunc' => 'TYPO3\\CMS\\Core\\Resource\\Service\\UserFileMountService->renderTceformsSelectDropdown',
                'default' => '',
            ],
        ],
    ],
];
