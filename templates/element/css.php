<?php
/**
 * @var \App\View\AppView $this
 */
if (!$this->helpers()->has('ViteScripts')) {
    $this->loadHelper('ViteHelper.ViteScripts', \Assets\Utilities\ViteScripts::getViteConfig());
}
?>
<?= $this->ViteScripts->head(['plugin' => 'Assets']) ?>
