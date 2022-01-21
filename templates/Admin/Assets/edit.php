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
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $asset->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $asset->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Assets'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="assets form content">
            <?= $this->Form->create($asset, ['type' => 'file']) ?>
            <fieldset>
                <legend><?= __('Edit Asset') ?></legend>
                <?= $asset->getThumbnail() ?>
                <?php
                    echo $this->Form->control('title');
                    echo $this->Form->control('description');
                    echo $this->Form->control('category', [
                        'options' => \App\Enum\AssetCategories::getList()
                    ]);
                    echo $this->Form->control('filename', ['type' => 'file']);
                ?>

            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
