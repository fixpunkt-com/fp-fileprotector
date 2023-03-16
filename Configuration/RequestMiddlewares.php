<?php
return [
    'frontend' => [
        'fp-fileprotector-access' => [
            'target' => \Fixpunkt\FpFileprotector\Middleware\AccessMiddleware::class,
            'before' => [
                'typo3/cms-frontend/page-resolver',
            ]
        ],
    ]
];