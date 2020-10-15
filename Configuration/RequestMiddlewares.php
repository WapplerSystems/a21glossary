<?php
return [
    'frontend' => [
        'wapplersystems/a21glossary' => [
            'target' => \WapplerSystems\A21glossary\Middleware\Glossary::class,
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering'
            ],
            'before' => [
                'typo3/cms-adminpanel/renderer',
                'typo3/cms-frontend/content-length-headers',
            ]
        ],
    ]
];