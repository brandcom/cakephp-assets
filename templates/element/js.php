<?php
/**
 * @var \App\View\AppView $this
 */
?>
<?php foreach (\Assets\Utilities\ViteScripts::js() as $path): ?>
    <?php
    $legacy = \Nette\Utils\Strings::contains($path, 'legacy');
    ?>
    <?= $this->Html->script($path, [
        'type' => $legacy ? 'false' : 'module',
        'nomodule' => $legacy ? 'nomodule' : false,
    ]) ?>
<?php endforeach; ?>
