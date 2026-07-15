<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$moduleIcon = (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() >= 14)
    ? [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:fp_fileprotector/Resources/Public/Icons/Modules/protection.svg',
    ]
    : [
        'provider' => BitmapIconProvider::class,
        'source' => 'EXT:fp_fileprotector/Resources/Public/Icons/Modules/protection.jpg',
    ];

return [
    'tx-fpfileprotector-module' => $moduleIcon,

    // Icons for Folder-Tree

    'tx-fpfileprotector-folder-protected' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:fp_fileprotector/Resources/Public/Icons/Locks/protected.svg',
    ],
    'tx-fpfileprotector-folder-public' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:fp_fileprotector/Resources/Public/Icons/Locks/public.svg',
    ],
    'tx-fpfileprotector-folder-no-access' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:fp_fileprotector/Resources/Public/Icons/Locks/no_access.svg',
    ],
];
