<?php

declare(strict_types=1);

defined('TYPO3') or die();

$table = 'tx_fpfileprotector_domain_model_protection';

// Fields required for the FE access check (see FeLoginAccessType).
$tempColumns = [
    'fe_login' => [
        'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.fe_login',
        'exclude' => 1,
        'onChange' => 'reload',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
        ]
    ],
    'user_groups' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.user_groups',
        'displayCond' => 'FIELD:fe_login:REQ:true',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectMultipleSideBySide',
            'foreign_table' => 'fe_groups',
            'MM' => 'tx_fpfileprotector_protection_fegroups_mm'
        ],
    ],
    'users' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.users',
        'displayCond' => 'FIELD:fe_login:REQ:true',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectMultipleSideBySide',
            'foreign_table' => 'fe_users',
            'MM' => 'tx_fpfileprotector_protection_feusers_mm'
        ],
    ],
];

// Add fields to the general record description.
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, $tempColumns);

// Group the FE fields in their own palette.
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    $table,
    'fe',
    'fe_login,--linebreak--,user_groups,--linebreak--,users'
);

// Render the palette in the backend form.
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    $table,
    '--palette--;LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:tx_fpfileprotector_domain_model_protection.palette.fe;fe'
);
