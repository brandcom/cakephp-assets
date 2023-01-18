<?php
declare(strict_types=1);

namespace Assets\View\Helper;

use Assets\Error\InvalidArgumentException;
use Assets\Error\MissingContextException;
use Assets\Model\Entity\Asset;
use Cake\ORM\Entity;
use Cake\View\Helper;

/**
 * AssetForm helper
 *
 * @property \Cake\View\Helper\FormHelper $Form
 * @property \Cake\View\Helper\HtmlHelper $Html
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

    /**
     * The Entity an Asset belongs to,
     *   e.g. a User which has a ProfilePicture (Asset)
     *
     * @var \Cake\ORM\Entity|null
     */
    private ?Entity $context;

    private ?Asset $asset;

    /**
     * @param array $config
     * @return void
     * @throws \Assets\Error\InvalidArgumentException
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setContext(null);
        $this->setAsset();
    }

    /**
     * Starts a form with type 'file'
     *
     * @param \Cake\ORM\Entity|null $context
     * @param array $options
     * @return string
     * @throws \Assets\Error\InvalidArgumentException
     * @see FormHelper::create()
     */
    public function create(?Entity $context = null, array $options = []): string
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
     * @throws \Assets\Error\InvalidArgumentException
     * @throws \Assets\Error\MissingContextException
     */
    public function control(string $fieldName, array $options = []): string
    {
        $this->setContext($options['context'] ?? null);

        if (!$this->context) {
            throw new MissingContextException(
                'Set a $context by starting the form through AssetFormHelper::create or by passing it through the $options array.'
            );
        }

        $associationName = $this->getAssociationName($fieldName);
        $this->setAsset($associationName);

        return $this->getView()->element('Assets.Helper/AssetForm/UploadField', [
            'associationName' => $this->getAssociationName($fieldName),
            'context' => $this->context,
            'asset' => $this->asset,
        ]);
    }

    /**
     * @param string $fieldName
     * @return string
     * @throws \Assets\Error\InvalidArgumentException
     */
    private function getAssociationName(string $fieldName): string
    {
        $association = str_replace(['_id', '.filename'], '', $fieldName);

        if (!array_key_exists($association, $this->context->getAccessible())) {
            throw new InvalidArgumentException("'{$association}_id' does not seem to exist on {$this->context->getSource()}.");
        }

        return $association;
    }

    /**
     * @param $context
     * @return void
     */
    private function setContext($context)
    {
        if ($context !== null && !is_a($context, Entity::class)) {
            throw new InvalidArgumentException('$context has to be an Entity.');
        }

        $this->context = $context;
    }

    private function setAsset(?string $associationName = null)
    {
        if (!$this->context) {
            $this->asset = null;

            return;
        }

        $asset = $this->context->get($associationName);
        if ($asset && is_a($asset, Asset::class)) {
            $this->asset = $asset;

            return;
        }

        $this->asset = null;
    }
}
