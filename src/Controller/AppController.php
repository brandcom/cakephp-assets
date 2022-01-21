<?php
declare(strict_types=1);

namespace Assets\Controller;

use App\Controller\AppController as BaseController;
use Assets\Model\Table\AssetsAssetsTable;

class AppController extends BaseController
{
    protected AssetsAssetsTable $Assets;

    public function initialize(): void
    {
        parent::initialize();
        $this->Assets = $this->fetchTable('Assets.AssetsAssets');
    }
}
