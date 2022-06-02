<?php
declare(strict_types=1);

namespace Assets\Controller;

use App\Controller\AppController as BaseController;

/**
 * @mixin \Cake\Controller\Controller
 */
class AppController extends BaseController
{
    /**
     * @var \Assets\Model\Table\AssetsTable
     */
    public $Assets;

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
