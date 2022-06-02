<?php
declare(strict_types=1);

namespace Assets\View\Helper;

use Assets\Error\MissingContextException;
use Assets\Model\Entity\Asset;
use Cake\ORM\Entity;
use Cake\View\Helper;

/**
 * AssetForm helper
 *
 * @property Helper\FormHelper $Form
 * @property Helper\HtmlHelper $Html
 */
class AssetFormHelper extends Helper
{
    /**
     * Default configuration.
     *
     * @var array<string, mixed>
     */
    protected $_defaultConfig = [];

    public $helpers = [
        'Form',
        'Html',
    ];

    private Entity|null $context;

    private Asset|null $asset;

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setContext(null);
        $this->setAsset();
    }

    /**
     * Starts a form with type 'file'
     *
     * @param Entity|null $context
     * @param array $options
     * @return string
     *
     * @see FormHelper::create()
     */
    public function create(Entity|null $context = null, array $options = []): string
    {
        $this->setContext($context);
        $options['type'] = 'file';

        return $this->Form->create($context, $options);
    }

    /**
     * Creates an upload field for the Assets plugin
     *
     * @param string $fieldName
     * @param array $options can contain 'context' if the form was not started through AssetFormHelper::create
     * @return string
     * @throws MissingContextException
     */
    public function control(string $fieldName, array $options = []): string
    {
        $this->setFieldContext($options);

        if (!$this->context) {
            throw new MissingContextException(
                'Set a $context by starting the form through AssetFormHelper::create or by passing it through the $options array.'
            );
        }

        $associationName = $this->getAssociationName($fieldName);
        $this->setAsset($associationName);

        $template = '';
        $existingAssetId = null;

        $template .= $this->getPreview();

        $template .= $this->Form->control(sprintf('%s.filename', $associationName), [
            'type' => 'file',
            'label' => __d('assets', 'Choose file'),
            'class' => 'js-assets-upload-field',
            'data-context' => json_encode([
                'entityId' => $this->context->id,
                'entityModel' => $this->context->getSource(),
                'associationName' => $associationName,
            ]),
            'required' => false,
        ]);

        $asset = $this->context->get($associationName);
        if ($asset && is_a($asset, Asset::class)) {
            $existingAssetId = $asset->id ?: null;
        }
        $template .= $this->Form->control(sprintf('%s_id', $associationName), [
            'value' => $existingAssetId,
            'type' => 'text',
            'label' => false,
            'class' => 'js-assets-existing-file',
            'required' => false,
        ]);

        return $this->Html->div('js-assets-upload-wrapper', $template, ['escape' => false]);
    }

    private function getAssociationName(string $fieldName): string
    {
        $association = str_replace(['_id', '.filename'], '', $fieldName);

        if (!array_key_exists($association, $this->context->getAccessible())) {
            throw new \InvalidArgumentException("'{$association}_id' does not seem to exist on {$this->context->getSource()}.");
        }

        return $association;
    }

    /**
     * Set the Form's context for an individual field.
     *
     * @param array $options
     * @return void
     */
    private function setFieldContext(array $options)
    {
        if (empty($options['context'])) {
            return;
        }

        $this->setContext($options['context']);
    }

    /**
     * @param $context
     * @return void
     */
    private function setContext($context)
    {
        if (null === $context) {
            $this->context = null;
            return;
        }

        if (!is_a($context, Entity::class)) {
            throw new \InvalidArgumentException('$context has to be an Entity.');
        }

        $this->context = $context;
    }

    private function setAsset(?string $associationName = null)
    {
        if (!$this->context) {
            $this->asset = null;
            return;
        };

        $asset = $this->context->get($associationName);
        if ($asset && is_a($asset, Asset::class)) {

            $this->asset = $asset;
            return;
        }

        $this->asset = null;
    }

    private function getPreview(): ?string
    {
        $template = '';

        if (!$this->asset) {

            return $this->Html->div('js-assets-preview', __d('assets', 'No file selected'), ['escape' => false]);
        }

        if ($this->asset->getThumbnail()) {
            $template .= $this->Html->div('js-assets-preview__image', $this->asset->getThumbnail(), ['escape' => false]);
        }

        $template .= $this->Html->div('js-assets-preview__fileinfo', sprintf('%s, %s', $this->asset->filetype, $this->asset->getFileSizeInfo()));

        return $this->Html->div('js-assets-preview', $template, ['escape' => false]);
    }
}
