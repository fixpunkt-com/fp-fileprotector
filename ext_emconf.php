<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'File Protector',
    'description' => 'Stellt einen Verzeichnisschutz zur Verfügung.',
    'category' => 'frontend',
    'author' => 'Yannik Börgener',
    'author_email' => 'y.boergener@fixpunkt.com',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.1.1',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.21-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
