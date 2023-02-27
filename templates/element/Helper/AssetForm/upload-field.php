<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\ORM\Entity|null $context Entity which holds the Asset
 * @var \Assets\Model\Entity\Asset|null $asset
 * @var string $associationName field name of the Asset on the entity (without '_id')
 */

$fileInfo = [
	'entityId' => $context->id,
	'entityModel' => $context->getSource(),
	'associationName' => $associationName,
	'asset' => $asset,
];

$pluginRoot = 'vendor' . DS . 'passchn' . DS . 'cakephp-assets';
$this->ViteScripts->script([
	'cssBlock' => 'assetsUploadFieldHead',
	'block' => 'assetsUploadFieldBody',
], \ViteHelper\Utilities\ViteHelperConfig::create([
	'forceProductionMode' => false,
	'build' => [
		'manifest' => ROOT . DS . $pluginRoot . DS . 'webroot' . DS . 'manifest.json',
	],
	'plugin' => 'Assets',
	'development' => [
		'url' => 'http://localhost:3005/assets',
		'scriptEntries' => [
			'webroot_src/components/UploadField/initUploadField.ts',
		],
	],
]));

?>

<?= $this->fetch('assetsUploadFieldHead') ?>
<div data-assets-upload-field>
	<div data-json>
		<?= json_encode($fileInfo, JSON_HEX_QUOT) ?>
	</div>
	<?= $this->Form->control($associationName . '_id', [
		'type' => 'text',
		'data-field',
		'tabindex' => '-1',
	]) ?>
</div>
<?= $this->fetch('assetsUploadFieldBody') ?>
