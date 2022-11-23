<?php
/**
 * @var \Assets\View\AppView $this
 * @var \Assets\Model\Entity\AssetsAsset $asset
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __d('assets','Actions') ?></h4>
            <?= $this->Html->link(__d('assets','List Assets'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="assets form content">
            <?= $this->Form->create($asset, ['type' => 'file']) ?>
            <fieldset>
                <legend><?= __d('assets','Add Asset') ?></legend>
                <?php
                    echo $this->Form->control('title');
                    echo $this->Form->control('description');
                    echo $this->Form->control('category');
                    echo $this->Form->control('filename', ['type' => 'file']);
                ?>
            </fieldset>
            <?= $this->Form->button(__d('assets','Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
