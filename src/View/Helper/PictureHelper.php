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
     * @param ImageAsset|null $image
     * @param int[] $widths
     * @param array $params
     * @return string|null
     * @throws \Exception
     */
    public function webp(?ImageAsset $image, array $widths = [300, 500], array $params = []): ?string
    {
        $this->image = $image;
        sort($widths);

        return $this->Html->tag('picture',
            $this->Html->tag('source', null, [
                'type' => 'image/webp',
                'srcset' => $this->getSrcSet('webp', $widths),
            ])
            . $this->Html->tag('source', null, [
                'type' => 'image/jpeg',
                'srcset' => $this->getSrcSet('jpeg', $widths),
            ])
            . $this->Html->image($this->image->scaleWidth(current($widths))->toJpg()->getPath(), $params)
        );
    }

    private function getSrcSet(string $format, array $widths): ?string
    {
        $links = [];

        foreach ($widths as $width) {

            if (!is_int($width)) {
                throw new \Exception("\$widths must be a list of integers. ");
            }

            $links[] = $this->getImageUrl($format, $width) . ' ' . $width . 'w';
        }

        return implode(', ', $links);
    }

    private function getImageUrl(string $format, int $width): string
    {
        switch (strtolower($format)) {
            case "webp":
                return $this->image->toWebp()->scaleWidth($width)->getPath();
            default:
                return $this->image->toJpg()->scaleWidth($width)->getPath();
        }
    }
}