<?php
declare(strict_types=1);

namespace Assets\View\Helper;

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
     * Returns an html picture element with a webp and jpeg source, and a fallback img element.
     *
     * @param ImageAsset|string|null $image
     * -> a path to a static file can be passed.
     *
     * @param int[] $widths
     * @param array $params
     * @return string|null
     * @throws \Exception
     */
    public function webp($image, array $widths = [300, 500], array $params = []): ?string
    {
        if (!$image) {
            return null;
        }

        if (is_string($image)) {
            $image = ImageAsset::createFromPath($image);
        }

        if (!is_a($image, ImageAsset::class)) {
            return null;
        }

        $this->image = $image;
        sort($widths);

        $sizes = $params['sizes'] ?? "100vw";
        unset($params['sizes']);

        $options = [
            'filename' => $params['filename'] ?? null,
        ];

        unset($params['filename']);
        $base_filename = $options['filename'] ? $options['filename'] . '-' . current($widths) . 'px' : null;

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

    private function getSrcSet(string $format, array $widths, array $options=[]): ?string
    {
        $links = [];

        foreach ($widths as $width) {

            if (!is_int($width)) {
                throw new \Exception("\$widths must be a list of integers. ");
            }

            $links[] = $this->getImageUrl($format, $width, $options) . ' ' . $width . 'w';
        }

        return implode(', ', $links);
    }

    private function getImageUrl(string $format, int $width, array $options): string
    {
        $filename = $options['filename'] ? $options['filename'] . '-' . $width . 'px' : null;

        switch (strtolower($format)) {
            case "webp":
                return $this->image->toWebp()->scaleWidth($width)->setFilename($filename)->getPath();
            default:
                return $this->image->toJpg()->scaleWidth($width)->setFilename($filename)->getPath();
        }
    }
}