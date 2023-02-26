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

    private Asset $asset;

	public function initialize(array $config): void
	{
		$this->context = null;
		parent::initialize($config);
	}

	/**
     * Starts a form with type 'file'
     *
     * @param \Cake\ORM\Entity $context the Form's context - cann also be passed AssetFormHelper::control() via $options
     * @param array $options options for the Form. 'type' => 'file' will always be set.
     * @return string
     */
    public function create(Entity $context, array $options = []): string
    {
        $this->context = $context;
        $options['type'] = 'file';

        return $this->Form->create($context, $options);
    }

    /**
     * Creates an upload field for the Assets plugin
     *
     * @param string $fieldName name of the field
     * @param array $options can contain 'context' if the form was not started through AssetFormHelper::create
     * @return string
     * @throws \Assets\Error\InvalidArgumentException
     * @throws \Assets\Error\MissingContextException
     */
    public function control(string $fieldName, array $options = []): string
    {
        if (!empty($options['context'])) {
            $this->context = $options['context'];
        }

        if (null === $this->context) {
            throw new MissingContextException(
                sprintf(
					'Set a $context for the field "%s" by starting the form through'
					. 'AssetFormHelper::create or by passing it through the $options array.',
					$fieldName,
				)
            );
        }

		$associationName = $this->getAssociationName($fieldName);
        $this->asset = $this->context->get($associationName);

        return $this->getView()->element('Assets.Helper/AssetForm/upload-field', [
            'associationName' => $associationName,
            'context' => $this->context,
            'asset' => $this->asset,
        ]);
    }

    /**
     * @param string $fieldName name of the field
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
}
