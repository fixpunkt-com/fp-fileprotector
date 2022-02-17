<?php
return array(
	'ctrl' => array(
        'title'	=> 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection',
        'label' => 'folder',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'searchFields' => 'folder',
        'iconfile' => 'EXT:fp_fileprotector/Resources/Public/Icons/Models/tx_fpfileprotector_domain_model_protection.svg'
    ),
    'palettes' => [
        'folder' => [
            'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.palette.folder',
            'showitem' => 'storage,folder',
        ],
        'fe' => [
            'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.palette.fe',
            'showitem' => 'fe_login,--linebreak--,user_groups,--linebreak--,users',
        ],
        'be' => [
            'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.palette.be',
            'showitem' => 'be_login',
        ],
    ],
    'types' => array(
        0 => ['showitem' => '--palette--;;folder,--palette--;;fe,--palette--;;be'],
    ),
	'columns' => array(
        'storage' => [
            'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.storage',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0]
                ],
                'foreign_table' => 'sys_file_storage',
                'foreign_table_where' => 'AND {#sys_file_storage}.{#protected} = 1',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'default' => 0,
            ]
        ],
        'folder' => [
            'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.folder',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [],
                'itemsProcFunc' => 'TYPO3\\CMS\\Core\\Resource\\Service\\UserFileMountService->renderTceformsSelectDropdown',
                'default' => '',
            ]
        ],
        'fe_login' => [
            'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.fe_login',
            'exclude' => 1,
            'onChange' => 'reload',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                    ]
                ],
            ]
        ],
        'be_login' => [
            'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.be_login',
            'exclude' => 1,
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                    ]
                ],
            ]
        ],
		'user_groups' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.user_groups',
            'displayCond' => 'FIELD:fe_login:REQ:true',
			'config' => array(
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'fe_groups',
                'MM' => "tx_fpfileprotector_protection_fegroups_mm"
			),
		),
        'users' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.users',
            'displayCond' => 'FIELD:fe_login:REQ:true',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'fe_users',
                'MM' => "tx_fpfileprotector_protection_feusers_mm"
            ),
        ),
	),
);