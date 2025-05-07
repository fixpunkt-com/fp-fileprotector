<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'File Protector',
    'description' => 'Stellt einen Verzeichnisschutz zur Verfügung.',
    'category' => 'frontend',
    'author' => 'Yannik Börgener',
    'author_email' => 'y.boergener@fixpunkt.com',
    'state' => 'stable',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
