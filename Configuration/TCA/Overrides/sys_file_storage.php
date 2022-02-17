<?php
defined('TYPO3_MODE') or die();

// Feld definieren
$tempColumns = [
    'protected' => [
        'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:sys_file_storage.protected',
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
    'protected_by_default' => [
        'label' => 'LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:sys_file_storage.protected_by_default',
        'exclude' => 1,
        'displayCond' => 'FIELD:protected:REQ:true',
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
    ]
];

// Feld der allgemeinen Datensatzbeschreibung hinzufügen - noch keine Ausgabe im Backend!
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file_storage', $tempColumns);

// Feld einer neuen Palette hinzufügen
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'sys_file_storage',
    'protection',
    'protected,protected_by_default'
);

// Neue Palette dem Tag hinzufügen, nach dem Titel - Dadurch Anzeige im Backend
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'sys_file_storage',
    '--palette--;LLL:EXT:fp_fileprotector/Resources/Private/Language/locallang.xlf:sys_file_storage.palette.protection;protection',
    '',
    'after:is_online'
);