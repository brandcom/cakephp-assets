<?php
declare(strict_types=1);

namespace Assets\View\Helper;

use Assets\Model\Entity\Asset;
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

    public function plainTextPreview(Asset $asset, array $options = []): string
    {
        if (!$asset->exists()) {
            return __('The Asset\'s file does not exist. ');
        }

        try {
            switch ($asset->filetype) {
                case 'csv':
                    return $this->csvPreview($asset, $options);
                default:
                    return $this->printFormatted($asset);
            }
        } catch (\Exception $e) {
            return __d('assets', "Error at TextAssetPreviewHelper: The Asset #{$asset->id}'s file with the filetype {$asset->filetype} is not readable. ");
        }

    }

    private function printFormatted(Asset $asset): string
    {
        return "<pre>" . h($asset->read()) . "</pre>";
    }

    private function csvPreview(Asset $asset, array $options = []): string
    {
        $reader = $asset->getCsvReader($options);
        $header = $reader->getHeader();
        $rows = $reader->getRecords();

        return $this->getView()->element('Assets.Helper/TextAssetPreview/csv-table', [
            'header' => $header,
            'rows' => $rows,
        ]);
    }
}
