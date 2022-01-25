<?php

use Cake\Core\Configure;

return [
    /**
     * Configuration for the Assets Plugin
     */
    'AssetsPlugin' => [
        'AssetsTable' => [
            // path where original files are saved.
            'assetsDir' => ROOT . DS . "resources" . DS . "assets" . DS,
            'displayField' => 'title',
            'Behaviors' => [],
        ],
        'ImageAsset' => [
            // driver can be 'imagick' or 'gd'
            'driver' => 'gd',
            // path where modified images are saved.
            'outDir' => DS . Configure::read('App.imageBaseUrl') . 'modified' . DS,
        ],
    ],
];
