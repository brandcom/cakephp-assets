<?php
declare(strict_types=1);

namespace Assets\Controller;

use Assets\Controller\AppController;
use function __;

/**
 * Assets Controller
 *
 * @method \Cake\ORM\Entity[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AssetsController extends AppController
{
    /**
     * View method
     *
     * @param string|null $id Asset id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $asset = $this->Assets->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('asset'));
    }

    /**
     * @return void
     *
     * Add ?download=1 to URL to download instead of view the file.
     */
    public function download(string $id)
    {
        $asset = $this->Assets->get($id);

        $disposition = $this->getRequest()->getQuery('download') ? 'attachment' : 'inline';

        header("Content-type: " . $asset->mimetype);
        header("Content-Disposition: $disposition; filename=\"{$asset->public_filename}\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        try {
            echo $asset->read();
        } catch (\Exception $e) {
            echo __("Die Datei kann nicht geöffnet werden. ");
        }

        exit;
    }
}
