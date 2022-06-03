<?php
/**
 * @var \App\View\AppView $this
 */

if (!$this->helpers()->has('ViteScripts')) {
    $this->ViteScripts = $this->loadHelper('ViteHelper.ViteScripts', \Assets\Utilities\ViteScripts::getViteConfig());
}
?>

<?= $this->ViteScripts->body(['plugin' => 'Assets']) ?>
