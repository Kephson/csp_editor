<?PHP

return [
    'frontend' => [
        'CspEditorViolationReportEndpoint-identifier' => [
            'target' => \RENOLIT\CspEditor\Middleware\CspViolationReportEndpoint::class,
            'before' => [
                'typo3/cms-frontend/eid',
            ],
            'after' => [
                'typo3/cms-frontend/preprocessing',
            ],
        ]
    ]
];
