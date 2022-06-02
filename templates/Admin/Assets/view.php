<?php
/**
 * @var \App\View\AppView $this
 * @var \Assets\Model\Entity\AssetsAsset $asset
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Asset'), ['action' => 'edit', $asset->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Asset'), ['action' => 'delete', $asset->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $asset->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Assets'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Asset'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="assets view content">
            <h3><?= h($asset->title) ?></h3>
            <?php if ($asset->isImage()): ?>
                <p>
                    <?= $asset->getImage(65)->scaleWidth(\Assets\Enum\ImageSizes::SM)->toJpg() ?>
                </p>
            <?php endif; ?>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($asset->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Title') ?></th>
                    <td><?= h($asset->title) ?></td>
                </tr>
                <tr>
                    <th><?= __('Category') ?></th>
                    <td><?= h($asset->category) ?></td>
                </tr>
                <tr>
                    <th><?= __('Filename') ?></th>
                    <td><?= h($asset->filename) ?></td>
                </tr>
                <tr>
                    <th><?= __('Directory') ?></th>
                    <td><?= h($asset->directory) ?></td>
                </tr>
                <tr>
                    <th><?= __('Mimetype') ?></th>
                    <td><?= h($asset->mimetype) ?></td>
                </tr>
                <tr>
                    <th><?= __('Filesize') ?></th>
                    <td><?= h($asset->getFileSizeInfo()) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($asset->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($asset->modified) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Description') ?></strong>
                <blockquote>
                    <?= h($asset->description); ?>
                </blockquote>
            </div>
            <?php if ($asset->isPlainText()): ?>
                <div class="text">
                    <strong><?= __('Inhalt') ?></strong>
                    <div>
                        <?= $this->TextAssetPreview->plainTextPreview($asset) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
