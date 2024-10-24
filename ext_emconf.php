<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'File Protector',
    'description' => 'Stellt einen Verzeichnisschutz zur Verfügung.',
    'category' => 'frontend',
    'author' => 'Yannik Börgener',
    'author_email' => 'y.boergener@fixpunkt.com',
    'state' => 'stable',
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
