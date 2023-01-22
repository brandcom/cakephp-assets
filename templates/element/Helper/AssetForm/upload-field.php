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
	'asset' => null,
];

if ($asset) {
	$fileInfo['asset'] = [
		'fileSize' => $asset->getFileSizeInfo(),
		'thumbnail' => $asset->getThumbnail(\Assets\Enum\ImageSizes::SM, false),
		'mimetype' => $asset->mimetype,
		'filename' => $asset->filename,
		'downloadLink' => $asset->getDownloadLink(),
	];
}

$i18n = [
	'uploadNew' => __d('assets', 'Upload new'),
	'chooseExisting' => __d('assets', 'Choose existing'),
];

$pluginRoot = 'vendor' . DS . 'passchn' . DS . 'cakephp-assets';
$this->ViteScripts->script([], \ViteHelper\Utilities\ViteHelperConfig::create([
	'forceProductionMode' => true,
	'build' => [
		'manifest' => ROOT . DS . $pluginRoot . DS . 'webroot' . DS . 'dist' . DS . 'manifest.json',
	],
	'plugin' => 'Assets',
	'development' => [
		'scriptEntries' => [
			$pluginRoot . DS . 'webroot_src/components/UploadField/initUploadField.ts',
		],
	],
]));

?>
<script
	src="<?= $this->Url->assetUrl('Assets.' . $scriptUrl) ?>"
	type="module"
></script>
<link rel="stylesheet" href="<?= $this->Url->assetUrl('Assets.' . $cssUrl) ?>">
<div data-vue-upload-field>
	<div class="vue-json" title="file-info" style="display: none;">
		<?= json_encode($fileInfo, JSON_HEX_QUOT | JSON_HEX_TAG) ?>
	</div>
	<div class="vue-json" title="i18n" style="display: none;">
		<?= json_encode($i18n, JSON_HEX_QUOT | JSON_HEX_TAG) ?>
	</div>
	<div class="vue-html" title="original-fields-template">
		<?= $this->Form->control(sprintf('%s.filename', $associationName), [
			'type' => 'file',
			'label' => __d('assets', 'Choose file'),
			'class' => 'js-assets-upload-field',
			'required' => false,
		]) ?>
	</div>
</div>
