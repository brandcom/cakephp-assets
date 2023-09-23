<?php
declare(strict_types=1);

namespace Assets\Controller\Admin;

use Assets\Controller\AppController;
use Cake\Http\CallbackStream;
use Cake\Http\Response;
use function Cake\I18n\__d;

/**
 * Assets Controller
 *
 * @method \Cake\Datasource\Paging\PaginatedInterface<\Assets\Model\Entity\Asset[]> paginate($object = null, array $settings = [])
 */
class AssetsController extends AppController
{
    /**
     * Index method
     *
     * @return void Renders view
     */
    public function index(): void
    {
        $assets = $this->paginate($this->Assets);

        $this->set(compact('assets'));
    }

    /**
     * View method
     *
     * @param string|null $id Asset id.
     * @return void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null): void
    {
        $asset = $this->Assets->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('asset'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add(): ?Response
    {
        $asset = $this->Assets->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $asset = $this->Assets->patchEntity($asset, $this->getRequest()->getData());
            if ($this->Assets->save($asset)) {
                $this->Flash->success(__d('assets', 'The asset has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__d('assets', 'The asset could not be saved. Please, try again.'));
        }
        $this->set(compact('asset'));

        return $this->render();
    }

    /**
     * Edit method
     *
     * @param string|null $id Asset id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null): ?Response
    {
        $asset = $this->Assets->get($id, [
            'contain' => [],
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $asset = $this->Assets->patchEntity($asset, $this->getRequest()->getData());
            if ($this->Assets->save($asset)) {
                $this->Flash->success(__d('assets', 'The asset has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__d('assets', 'The asset could not be saved. Please, try again.'));
        }
        $this->set(compact('asset'));

        return $this->render();
    }

    /**
     * Delete method
     *
     * @param string|null $id Asset id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null): ?Response
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $asset = $this->Assets->get($id);
        if ($this->Assets->delete($asset)) {
            $this->Flash->success(__d('assets', 'The asset has been deleted.'));
        } else {
            $this->Flash->error(__d('assets', 'The asset could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Add ?download=1 to URL to download instead of view the file.
     *
     * @param string $id The ID of the asset
     * @return \Cake\Http\Response|null
     */
    public function download(string $id): ?Response
    {
        $asset = $this->Assets->get($id);
        $is_download = (bool)$this->getRequest()->getQuery('download');

        $stream = new CallbackStream(function () use ($asset) {
            return $asset->read();
        });

        $response = $this->getResponse()
            ->withType($asset->mimetype ?? 'jpg')
            ->withDisabledCache()
            ->withBody($stream);

        return $is_download ? $response->withDownload($asset->public_filename) : $response;
    }
}
