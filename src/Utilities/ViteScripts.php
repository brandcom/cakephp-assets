<?php
declare(strict_types=1);

namespace Assets\Utilities;

class ViteScripts
{
    /**
     * @return array
     */
    public static function getViteConfig(): array
    {
        return [
            'forceProductionMode' => true,
            'devHostNeedles' => [
                '.test',
                'localhost',
                '127.0.0.1',
                '.local',
            ],
            // for Cookies or URL-params to force production mode
            'productionHint' => 'vprod',
            'devPort' => 3000,
            'mainJs' => 'main.ts',
            'baseDir' => ROOT . DS . 'vendor' . DS . 'passchn' . DS . 'cakephp-assets' . DS . 'webroot',
        ];
    }
}
