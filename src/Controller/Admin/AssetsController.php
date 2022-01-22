<?php
declare(strict_types=1);

namespace Assets\Controller\Admin;

use Assets\Controller\AppController;
use Cake\Http\CallbackStream;
use Cake\Http\Response;
use function __;

/**
 * Assets Controller
 *
 * @method \Cake\ORM\Entity[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AssetsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $assets = $this->paginate($this->AssetsAssets);

        $this->set(compact('assets'));
    }

    /**
     * View method
     *
     * @param string|null $id Asset id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $asset = $this->AssetsAssets->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('asset'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $asset = $this->AssetsAssets->newEmptyEntity();
        if ($this->request->is('post')) {
            $asset = $this->AssetsAssets->patchEntity($asset, $this->request->getData());
            if ($this->AssetsAssets->save($asset)) {
                $this->Flash->success(__('The asset has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The asset could not be saved. Please, try again.'));
        }
        $this->set(compact('asset'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Asset id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $asset = $this->AssetsAssets->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $asset = $this->AssetsAssets->patchEntity($asset, $this->request->getData());
            if ($this->AssetsAssets->save($asset)) {
                $this->Flash->success(__('The asset has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The asset could not be saved. Please, try again.'));
        }
        $this->set(compact('asset'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Asset id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $asset = $this->AssetsAssets->get($id);
        if ($this->AssetsAssets->delete($asset)) {
            $this->Flash->success(__('The asset has been deleted.'));
        } else {
            $this->Flash->error(__('The asset could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Add ?download=1 to URL to download instead of view the file.
     */
    public function download(string $id): Response
    {
        $asset = $this->AssetsAssets->get($id);
        $is_download = (bool)$this->getRequest()->getQuery('download');

        $stream = new CallbackStream(function () use ($asset) {
            return $asset->read();
        });

        $response = $this->response
            ->withType($asset->mimetype)
            ->withDisabledCache()
            ->withBody($stream);

        return $is_download ? $response->withDownload($asset->public_filename) : $response;
    }
}
