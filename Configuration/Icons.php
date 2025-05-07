<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'tx-fpfileprotector-module' => [
        'provider' => BitmapIconProvider::class,
        'source' => 'EXT:fp_fileprotector/Resources/Public/Icons/Modules/protection.jpg',
    ],
];
