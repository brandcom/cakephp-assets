<?php
declare(strict_types=1);

namespace Assets\View\Helper;

use Assets\Error\InvalidArgumentException;
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
     * @param \Assets\Utilities\ImageAsset|null $image The asset object
     * @param int[] $widths Array of width which will be present in the SrcSet
     * @param array $params Addition params like sizes
     * @return string|null
     * @throws \Exception
     */
    public function webp(?ImageAsset $image, array $widths = [300, 500], array $params = []): ?string
    {
        if (!$image) {
            return null;
        }

        $this->image = $image;
        sort($widths);

        $sizes = $params['sizes'] ?? '100vw';
        unset($params['sizes']);

        return $this->Html->tag(
            'picture',
            $this->Html->tag('source', null, [
                'type' => 'image/webp',
                'srcset' => $this->getSrcSet('webp', $widths),
                'sizes' => $sizes,
            ])
            . $this->Html->tag('source', null, [
                'type' => 'image/jpeg',
                'srcset' => $this->getSrcSet('jpeg', $widths),
                'sizes' => $sizes,
            ])
            . $this->image->scaleWidth(current($widths))->toJpg()->getHTML($params)
        );
    }

    /**
     * @param string $format Which type of image you want
     * @param array $widths Array of image widths you desire to have in the SrcSet
     * @return string|null
     * @throws \Assets\Error\InvalidArgumentException
     */
    private function getSrcSet(string $format, array $widths): ?string
    {
        $links = [];

        foreach ($widths as $width) {
            if (!is_int($width)) {
                throw new InvalidArgumentException('$widths must be a list of integers.');
            }

            $links[] = $this->getImageUrl($format, $width) . ' ' . $width . 'w';
        }

        return implode(', ', $links);
    }

    /**
     * @param string $format Which type of image you want
     * @param int $width The width how the image should be output
     * @return string
     * @throws \Exception
     */
    private function getImageUrl(string $format, int $width): string
    {
        switch (strtolower($format)) {
            case 'webp':
                return $this->image->toWebp()->scaleWidth($width)->getPath();
            default:
                return $this->image->toJpg()->scaleWidth($width)->getPath();
        }
    }
}
