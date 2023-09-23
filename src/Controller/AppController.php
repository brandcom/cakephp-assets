<?php
declare(strict_types=1);

namespace Assets\Controller;

use Assets\Model\Table\AssetsTable;
use Cake\Controller\Controller as BaseController;

class AppController extends BaseController
{
    /**
     * @var \Assets\Model\Table\AssetsTable
     */
    public AssetsTable $Assets;

    /**
     * Initialize the Assets table object
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Assets = $this->fetchTable('Assets.Assets');
    }
}
