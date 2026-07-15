<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'File Protector',
    'description' => 'Restricts access to file storages based on frontend login, user groups, or backend session. Also allows to easily add new access rules via own access classes.',
    'category' => 'fe',
    'state' => 'stable',
    'author' => 'fixpunkt für digitales GmbH',
    'author_email' => 'office@fixpunkt.com',
    'author_company' => 'fixpunkt für digitales GmbH',
    'version' => '3.3.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];