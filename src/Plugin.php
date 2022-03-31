<?php
declare(strict_types=1);

namespace Assets;

use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;

/**
 * Plugin for Assets
 */
class Plugin extends BasePlugin
{
    /**
     * Load all the plugin configuration and bootstrap logic.
     *
     * The host application is provided as an argument. This allows you to load
     * additional plugin dependencies, or attach events.
     *
     * @param \Cake\Core\PluginApplicationInterface $app The host application
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

        if (file_exists(ROOT . DS . 'config' . DS . 'app_assets.php')) {
            Configure::load('app_assets');
        }

        $app->addPlugin('Josegonzalez/Upload');
    }
}
