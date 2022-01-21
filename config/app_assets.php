<?php

return [
    /**
     * Configuration for the Assets Plugin
     */
    'AssetsPlugin' => [
        'AssetsTable' => [
            'DisplayField' => 'title',
            'Behaviors' => [],
        ],
        'ImageAsset' => [
            // driver can be 'imagick' or 'gd'
            'driver' => 'gd'
        ]
    ],
];
