<?php
/**
 * @var \App\View\AppView $this
 * @var \Assets\Model\Entity\AssetsAsset[]|\Cake\Collection\CollectionInterface $assets
 */

use function Cake\Core\h;

?>
<div class="assets index content">
    <?= $this->Html->link(__d('assets', 'New Asset'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __d('assets', 'Assets') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>
                        <?= $this->Paginator->sort('title') ?> /
                        <?= $this->Paginator->sort('filesize') ?>
                    </th>
                    <th>
                        <?= $this->Paginator->sort('category') ?> /
                        <?= $this->Paginator->sort('filename') ?>
                    </th>
                    <th>
                        <?= $this->Paginator->sort('created') ?> /
                        <?= $this->Paginator->sort('modified') ?>
                    </th>
                    <th><?= __d('assets', 'Preview') ?></th>
                    <th class="actions"><?= __d('assets', 'Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assets as $asset): ?>
                <tr>
                    <td>
                        <?= h($asset->title) ?>
                        <br>
                        <small>
                            <?= $asset->getFileSizeInfo() ?>
                        </small>
                    </td>
                    <td>
                        <p>
                            <?= h($asset->category) ?>
                        </p>
                        <p>
                            <small><?= h($asset->filename) ?></small>
                        </p>
                    </td>
                    <td>
                        <p>
                            <?= h($asset->created) ?>
                        </p>
                        <p>
                            <?= h($asset->modified) ?>
                        </p>
                    </td>
                    <td class="actions">
                        <?= $this->Html->link($asset->getThumbnail() ?: 'Öffnen', ['action' => 'download', $asset->id, '?' => ['download' => $asset->isViewableInBrowser() ? 0 : 1]], ['escape' => false]) ?>
                    </td>
                    <td class="actions">
                        <?= $this->Html->link(__d('assets', 'View'), ['action' => 'view', $asset->id]) ?>
                        <?= $this->Html->link(__d('assets', 'Edit'), ['action' => 'edit', $asset->id]) ?>
                        <?= $this->Form->postLink(__d('assets', 'Delete'), ['action' => 'delete', $asset->id], ['confirm' => __d('assets', 'Are you sure you want to delete # {0}?', $asset->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __d('assets', 'first')) ?>
            <?= $this->Paginator->prev('< ' . __d('assets', 'previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__d('assets', 'next') . ' >') ?>
            <?= $this->Paginator->last(__d('assets', 'last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->limitControl() ?></p>
        <p><?= $this->Paginator->counter(__d('assets', 'Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
