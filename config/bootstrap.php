<?php

use Cake\Core\Configure;

if (file_exists(ROOT . DS . 'config' . DS . 'app_assets.php')) {
    Configure::load('app_assets');
}