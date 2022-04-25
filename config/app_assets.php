<?php

use Cake\Core\Configure;

return [
    /**
     * Configuration for the Assets Plugin
     *
     * This file is not actually read by the plugin.
     * To change the default config, copy the file into your config directory or the array into any existing config file.
     */
    'AssetsPlugin' => [
        'AssetsTable' => [
            // path where original files are saved.
            'assetsDir' => "resources" . DS . "assets" . DS,
            'displayField' => 'title',
            'Behaviors' => [],
        ],
        'ImageAsset' => [
            // driver can be 'imagick' or 'gd'
            'driver' => 'gd',
            // path where modified images are saved.
            'outDir' => DS . Configure::read('App.imageBaseUrl') . 'modified' . DS,
        ],
        'Routes' => [
            'adminPrefix' => 'admin',
        ],
    ],
];
