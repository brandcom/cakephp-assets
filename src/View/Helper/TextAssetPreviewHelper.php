<?php
declare(strict_types=1);

namespace Assets\View\Helper;

use Assets\Model\Entity\AssetsAsset;
use Cake\View\Helper;

/**
 * AssetContent helper
 */
class TextAssetPreviewHelper extends Helper
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function plainTextPreview(AssetsAsset $asset, array $options = []): string
    {
        if (!$asset->exists()) {
            return __('The Asset\'s file does not exist. ');
        }

        return match ($asset->filetype) {
            'csv' => $this->csvPreview($asset, $options),
            default => '<pre>' . h($asset->read()) . '</pre>',
        };
    }

    private function csvPreview(AssetsAsset $asset, array $options = []): string
    {
        $reader = $asset->getCsvReader($options);
        $header = $reader->getHeader();
        $rows = $reader->getRecords();

        return $this->getView()->element('Helper/TextAssetPreview/csv-table', [
            'header' => $header,
            'rows' => $rows,
        ]);
    }
}
