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
     * @var array<string, mixed>
     */
    protected $_defaultConfig = [];

    /**
     * @param \Assets\Model\Entity\Asset $asset The asset entity
     * @param array $options Options passed to the CSV Reader
     * @return string
     */
    public function plainTextPreview(Asset $asset, array $options = []): string
    {
        if (!$asset->exists()) {
            return __d('assets', 'The Asset\'s file does not exist.');
        }

        try {
            switch ($asset->filetype) {
                case 'csv':
                    return $this->csvPreview($asset, $options);
                default:
                    return $this->printFormatted($asset);
            }
        } catch (\Exception $e) {
            return __d('assets', "Error at TextAssetPreviewHelper: The Asset #{0}'s " .
            'file with the filetype {1} is not readable.', $asset->id, $asset->filetype);
        }
    }

    /**
     * @param \Assets\Model\Entity\Asset $asset The asset entity
     * @return string
     * @throws \Exception
     */
    private function printFormatted(Asset $asset): string
    {
        return '<pre>' . h($asset->read()) . '</pre>';
    }

    /**
     * @param \Assets\Model\Entity\Asset $asset The asset entity
     * @param array $options Options passed to the CSV Reader
     * @return string
     * @throws \League\Csv\Exception
     * @throws \League\Csv\InvalidArgument
     */
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
