<?php
declare(strict_types=1);

namespace Assets\Controller;

use Assets\Model\Table\AssetsAssetsTable;
use Cake\Controller\Controller;

class AppController extends Controller
{
    protected AssetsAssetsTable $Assets;

    public function initialize(): void
    {
        parent::initialize();
        $this->Assets = $this->fetchTable('Assets.AssetsAssets');
    }
}
