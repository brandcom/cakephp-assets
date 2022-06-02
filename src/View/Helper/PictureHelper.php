<?php
declare(strict_types=1);

namespace Assets\View\Helper;

use Assets\Error\InvalidArgumentException;
use Assets\Model\Entity\Asset;
use Assets\Utilities\ImageAsset;
use Cake\View\Helper;

/**
 * Picture helper
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class PictureHelper extends Helper
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public $helpers = [
        'Html',
    ];

    private ImageAsset $image;

    /**
     * Returns a html picture element with a webp and jpeg source, and a fallback img element.
     *
     * @param \Assets\Utilities\ImageAsset|Asset|string|null $image The Asset object, ImageAsset or a path to a static file
     * @param int[] $widths Array of width which will be present in the SrcSet
     * @param array $params Addition params for the <img> tag. Takes special parameters 'sizes' and 'filename'.
     * @return string|null
     * @throws \Exception
     */
    public function webp($image, array $widths = [300, 500], array $params = []): ?string
    {
        if (!$image) {

            return null;
        }

        /**
         * If a path to a static image, or an Asset is passed, convert it to an ImageAsset
         */
        if (is_string($image)) {
            $image = ImageAsset::createFromPath($image);
        } elseif (is_a($image, Asset::class)) {
            if (!$image->isImage()) {

                return null;
            }

            $image = $image->getImage();
        }

        if (!is_a($image, ImageAsset::class)) {
            throw new \http\Exception\InvalidArgumentException('$image must be passed as an ImageAsset, an Asset which represents an image, or an absolute path (string) to a static image file.');
        }

        $this->image = $image;
        sort($widths);

        $options = [];

        $sizes = $params['sizes'] ?? '100vw';
        unset($params['sizes']);

        $options['filename'] = $params['filename'] ?? null;
        $base_filename = $options['filename'] ? $options['filename'] . '-' . current($widths) . 'px' : null;
        unset($params['filename']);

        return $this->Html->tag('picture',
            $this->Html->tag('source', null, [
                'type' => 'image/webp',
                'srcset' => $this->getSrcSet('webp', $widths, $options),
                'sizes' => $sizes,
            ])
            . $this->Html->tag('source', null, [
                'type' => 'image/jpeg',
                'srcset' => $this->getSrcSet('jpeg', $widths, $options),
                'sizes' => $sizes,
            ])
            . $this->image->scaleWidth(current($widths))->setFilename($base_filename)->toJpg()->getHTML($params)
        );
    }

    /**
     * @param string $format Which type of image you want
     * @param array $widths Array of image widths you desire to have in the SrcSet
     * @param array $options - passed to PictureHelper::getImageUrl
     * @return string|null
     * @throws \Assets\Error\InvalidArgumentException
     */
    private function getSrcSet(string $format, array $widths, array $options=[]): ?string
    {
        $links = [];

        foreach ($widths as $width) {
            if (!is_int($width)) {
                throw new InvalidArgumentException('$widths must be a list of integers.');
            }

            $links[] = $this->getImageUrl($format, $width, $options) . ' ' . $width . 'w';
        }

        return implode(', ', $links);
    }

    /**
     * @param string $format Which type of image you want
     * @param int $width The width how the image should be output
     * @param array $options
     * @return string
     * @throws \Exception
     */
    private function getImageUrl(string $format, int $width, array $options): string
    {
        $filename = $options['filename'] ? $options['filename'] . '-' . $width . 'px' : null;

        switch (strtolower($format)) {
            case 'webp':
                return $this->image->toWebp()->scaleWidth($width)->setFilename($filename)->getPath();
            default:
                return $this->image->toJpg()->scaleWidth($width)->setFilename($filename)->getPath();
        }
    }
}
