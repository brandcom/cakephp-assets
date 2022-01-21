<?php
/**
 * @var \Assets\View\AppView $this
 * @var \Assets\Model\Entity\AssetsAsset[]|\Cake\Collection\CollectionInterface $assets
 */
?>
<div class="assets index content">
    <?= $this->Html->link(__('New Asset'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Assets') ?></h3>
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
                    <th>Vorschau</th>
                    <th class="actions"><?= __('Actions') ?></th>
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
                        <?= $this->Html->link(__('View'), ['action' => 'view', $asset->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $asset->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $asset->id], ['confirm' => __('Are you sure you want to delete # {0}?', $asset->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
