<?php

declare(strict_types=1);

namespace Assets\Controller\Admin;

use Assets\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Log\Log;

class FilePoolController extends AppController
{
	/**
	 * @param EventInterface $event
	 * @return Response|void|null
	 */
	public function beforeFilter(EventInterface $event)
	{
		parent::beforeFilter($event);
		$this->FormProtection->setConfig('unlockedActions', ['query']);
		$this->getRequest()->allowMethod('post');
	}

	/**
	 * @return Response
	 */
	public function query(): Response
	{
		$context = $this->getRequest()->getData('context');

		$assets = $this->fetchTable('Assets.Assets')
			->find()
			->orderDesc('Assets.created')
			->where([
				'filename is not' => null,
				'filename !=' => '',
			])
			->limit(25)
			->toArray();

		return $this->getResponse()
			->withStringBody(json_encode([
				'success' => true,
				'context' => $context,
				'assets' => $assets,
			]));
	}

	/**
	 * @return Response
	 */
	public function getAsset(): Response
	{
		$id = $this->getRequest()->getData('id');

		return $this->getResponse()
			->withStringBody(json_encode([
				'asset' => $this->fetchTable('Assets.Assets')->get($id),
			]));
	}
}
