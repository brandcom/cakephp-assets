<?php

namespace Assets\Utilities;

use ViteHelper\Utilities\ViteManifest;

class ViteScripts
{
    private ViteManifest $manifest;

    public static function getManifest(): ViteManifest
    {
        return new ViteManifest([
            //'manifestDir' => ROOT . DS . 'vendor' . DS . 'passchn' . DS . 'cakephp-assets' . DS . 'webroot' . DS . 'manifest.json',
            'baseDir' => ROOT . DS . 'plugins' . DS . 'Assets' . DS . 'webroot',
        ]);
    }

    public static function css(): array
    {
        return array_map(
            function ($src) {
                return sprintf('Assets.%s', $src);
            },
            self::getManifest()->getCssFiles()
        );
    }

    public static function js(): array
    {
        return array_map(
            function ($src) {
                return sprintf('Assets.%s', $src);
            },
            self::getManifest()->getJsFiles()
        );
    }
}
