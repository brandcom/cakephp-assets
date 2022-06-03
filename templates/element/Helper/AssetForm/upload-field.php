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

?>
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
        <?= $this->Form->control(sprintf('%s_id', $associationName), [
            'value' => $context->get($associationName) ? $context->get($associationName)->get('id') : '',
            'type' => 'text',
            'label' => false,
            'class' => 'js-assets-existing-file',
            'required' => false,
        ]) ?>
    </div>
</div>
