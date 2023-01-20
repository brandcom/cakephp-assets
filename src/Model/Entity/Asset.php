<?php
declare(strict_types=1);

namespace Assets\Model\Entity;

use Assets\Enum\ImageSizes;
use Assets\Error\FileNotFoundException;
use Assets\Error\InvalidAssetTypeException;
use Assets\Utilities\ImageAsset;
use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\Routing\Router;
use League\Csv\Reader;
use Nette\Utils\Arrays;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

/**
 * Asset Entity
 *
 * @property string $id
 * @property string $title
 * @property string|null $description
 * @property string|null $category
 * @property string $filename
 * @property string $directory
 * @property string|null $mimetype
 * @property string $filesize
 * @property string $filetype
 * @property string $absolute_path
 * @property string $public_filename
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class Asset extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'title' => true,
        'description' => true,
        'category' => true,
        'filename' => true,
        'directory' => true,
        'mimetype' => true,
        'filesize' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * When submitting a Form with a file upload field which is left blank,
     * $this->filename will be an invalid Laminas\Diactoros\UploadedFile.
     *
     * Therefore, this function checks if the field is a string to get a valid response.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return file_exists($this->absolute_path);
    }

    /**
     * @return string
     */
    protected function _getAbsolutePath(): string
    {
        return ROOT . DS . $this->directory . DS . $this->filename;
    }

    /**
     * @return string
     * @throws \Assets\Error\FileNotFoundException
     */
    public function read(): string
    {
        if (!$this->exists()) {
            throw new FileNotFoundException("The File {$this->filename} for the Asset #{$this->id} does not exist in {$this->directory}.");
        }

        return FileSystem::read($this->absolute_path);
    }

    /**
     * @return string
     */
    protected function _getPublicFilename(): string
    {
        return $this->title ? Strings::webalize($this->title) . '.' . $this->filetype : $this->filename;
    }

    /**
     * @return string|null
     */
    protected function _getFiletype(): ?string
    {
        return Strings::after($this->filename, '.', -1);
    }

    /**
     * Returns true if the file's mimetype typically can be viewed in browsers,
     * e.g. images, videos, pdfs
     *
     * @return bool
     */
    public function isViewableInBrowser(): bool
    {
        if (!$this->get('id')) {
            return false;
        }

        return Arrays::contains([
                'image',
                'video',
            ], Strings::before((string)$this->mimetype, '/'))
            || Arrays::contains([
                'pdf',
                'json',
            ], Strings::after((string)$this->mimetype, '/'));
    }

    /**
     * Checks if the original file exists and if the file has an image/* mimetype (no svg).
     *
     * @return bool
     */
    public function isImage(): bool
    {
        return $this->exists()
            && Strings::before($this->mimetype ?? '', '/') === 'image'
            && !Strings::contains((string)Strings::after($this->mimetype ?? '', '/'), 'svg');
    }

    /**
     * @return bool
     */
    public function isPlainText(): bool
    {
        return $this->exists()
            && Strings::before($this->mimetype ?? '', '/') === 'text';
    }

    /**
     * @param int $quality Image quality between 0 - 100
     * @return \Assets\Utilities\ImageAsset
     * @throws \Assets\Error\FileNotFoundException
     * @throws \Assets\Error\InvalidAssetTypeException
     */
    public function getImage(int $quality = 90): ImageAsset
    {
        if (!$this->isImage()) {
            if (!$this->exists()) {
                throw new FileNotFoundException("Cannot call Asset::getImage() on #{$this->id}. The Asset's source file does not exist.");
            }

            throw new InvalidAssetTypeException("Cannot call Asset::getImage() on #{$this->id} with MimeType {$this->mimetype}.");
        }

        return new ImageAsset($this, $quality);
    }

    /**
     * @param int $size The size of the image
     * @param bool $html True if HTML, false if the path should be returned
     * @return string|null
     */
    public function getThumbnail(int $size = ImageSizes::THMB, bool $html = true): ?string
    {
        if (!$this->exists()) {
            return __d('assets', 'File not found.');
        }

        if ($this->isImage()) {
            try {
                $thumbnail = $this->getImage(65)->scaleWidth($size)->setCSS('asset-thumbnail')->toJpg();

                return $html ? $thumbnail->getHTML() : $thumbnail->getPath();
            } catch (\Exception $e) {
                return __d('assets', 'Cannot get ImageAsset.');
            }
        }

        return null;
    }

    /**
     * @return string
     */
    protected function _getFullTitle(): string
    {
        return $this->title . ' (' . $this->mimetype . ')';
    }

    /**
     * @return string
     */
    public function getFileSizeInfo(): string
    {
        $filesize = (int)$this->filesize;

        switch ($filesize) {
            case $filesize > 500000000:
                return round($filesize / 1000000000, 1) . ' GB';
            case $filesize > 500000:
                return round($filesize / 1000000, 1) . ' MB';
            case $filesize > 500:
                return round($filesize / 1000, 1) . ' kB';
            default:
                return $filesize . ' Byte';
        }
    }

    /**
     * @param array $options CSV options
     * @return \League\Csv\Reader
     * @throws \League\Csv\Exception
     * @throws \League\Csv\InvalidArgument
     * @throws \Assets\Error\InvalidAssetTypeException
     * @throws \Exception
     */
    public function getCsvReader(array $options = []): Reader
    {
        if ($this->filetype !== 'csv') {
            throw new InvalidAssetTypeException("The Asset {$this->title} is not a csv.");
        }

        $reader = Reader::createFromString($this->read());
        $reader->setDelimiter($options['csv_delimiter'] ?? ';');
        $reader->setHeaderOffset($options['csv_header_offset'] ?? 0);

        return $reader;
    }

    /**
     * @param bool $force_download True to force the user to download the file when link is clicked
     * @return string
     */
    public function getDownloadLink(bool $force_download = false): string
    {
        $download = $this->isViewableInBrowser() ? '0' : '1';
        if ($force_download) {
            $download = '1';
        }

        return Router::url([
            'plugin' => 'Assets',
            'prefix' => Configure::read('AssetsPlugin.Routes.adminPrefix', 'Admin'),
            'controller' => 'Assets',
            'action' => 'download',
            $this->id,
            '?' => [
                'download' => $download,
            ],
        ]);
    }

    /**
     * Will be removed due to naming inconsistency. Files are actually copied and not movedd. Use Asset::copyToWebroot instead.
     *
     * @param string|null $filename Name of the file. Default will be the Asset's id (uuid)
     * @param string $path Path from webroot where the copy will be available
     * @param array $config See above
     * @return string
     * @throws \Assets\Error\FileNotFoundException
     * @deprecated
     */
    public function moveToWebroot(?string $filename = null, string $path = 'files', array $config = []): string
    {
        return $this->copyToWebroot($filename, $path, $config);
    }

    /**
     * Copy the file to a folder in webroot
     *
     * $config:
     * - no_prefix:
     *  If not set to true, a prefix will be prepended to the filename depending on the modification date.
     *  If set to true, new Versions will not be detected. You will have to change the filename accordingly, or empty the folder set in $path.
     *
     * @param string|null $filename Name of the file. Default will be the Asset's id (uuid)
     * @param string $path Path from webroot where the copy will be available
     * @param array $config See above
     * @return string
     * @throws \Assets\Error\FileNotFoundException
     */
    public function copyToWebroot(?string $filename = null, string $path = 'files', array $config = []): string
    {
        $default_config = [
            'no_prefix' => false,
        ];

        $config = array_merge($default_config, $config);

        $prefix = null;
        if (empty($config['no_prefix'])) {
            $prefix = Strings::substring(md5($this->modified->toDateTimeString()), 0, 3) . '-';
        }

        $path = str_replace(WWW_ROOT, '', $path);
        $path = ltrim($path, '/');
        $path = WWW_ROOT . $path . DS;
        $filename = $filename ? $prefix . $filename : $prefix . $this->id;
        $filename = str_replace($this->filetype, '', $filename);
        $filename = rtrim($filename, '.');
        $filename = $filename . '.' . $this->filetype;
        $full_path = $path . $filename;

        if (file_exists($full_path)) {
            return str_replace(WWW_ROOT, '', $full_path);
        }

        if (!$this->exists()) {
            throw new FileNotFoundException("The file for Asset #{$this->id} does not exist. ");
        }

        FileSystem::copy($this->absolute_path, $full_path);

        return str_replace(WWW_ROOT, '', $full_path);
    }
}
