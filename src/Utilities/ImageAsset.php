<?php
declare(strict_types=1);

namespace Assets\Utilities;

use App\View\AppView;
use Assets\Error\FileNotFoundException;
use Assets\Error\FilterNotFoundException;
use Assets\Error\UnkownErrorException;
use Assets\Model\Entity\Asset;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\View\Helper\HtmlHelper;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Nette\Utils\Strings;

/**
 * To be called through Assets::getImage() with MimeType image/*
 *
 * To get an HTML img-Element, the object can be either echoed out directly,
 * which invokes the __toString() method, or the getHTML() method
 * can be called with the identical result.
 *
 * To just get a public path from /webroot, call getPath()
 */
class ImageAsset
{
    private Asset $asset;

    /**
     * Quality for jpg-compression. Call toJpg().
     * Does not work on other formats.
     */
    private int $quality;

    private array $modifications;

    private ?Image $image;

    private ?string $format;

    private ?string $filename;

    private string $css;

    private bool $lazyLoading;

    private string $outputDirectory;

    /**
     * @param \Assets\Model\Entity\Asset $asset The asset entity
     * @param int $quality Quality between 0 - 100
     */
    public function __construct(Asset $asset, int $quality = 90)
    {
        $this->asset = $asset;
        $this->quality = $quality;
        $this->modifications = [];
        $this->image = null;
        $this->format = $this->asset->filetype;
        $this->filename = null;
        $this->lazyLoading = true;
        $this->css = 'image-asset';
        $this->outputDirectory = Configure::read(
            'AssetsPlugin.ImageAsset.outDir',
            DS . Configure::read('App.imageBaseUrl') . 'modified' . DS
        );

        $this->trackModification('constructor', ['quality' => $quality], true);
    }

    /**
     * Create an instance from a static file.
     *
     * @param string $path absolute path or image in /img folder
     * @param array $options - optional:
     * - title (string): for alt-parameter in html-output
     * - quality (int): for jpg compression
     * @return $this
     */
    public static function createFromPath(string $path, array $options = [])
    {
        $absolute_path = null;
        $img_dir = WWW_ROOT . Configure::read('App.imageBaseUrl');
        if (file_exists($path)) {
            $absolute_path = $path;
        } elseif (file_exists($img_dir . $path)) {
            $absolute_path = $img_dir . $path;
        }

        if (!$absolute_path) {
            throw new \Exception("Could not find image with path {$path}.");
        }

        $splFileInfo = new \SplFileInfo($absolute_path);

        $asset = new Asset();
        $asset->id = md5($path);
        $asset->filename = $splFileInfo->getFilename();
        $asset->directory = ltrim(str_replace(ROOT, '', $splFileInfo->getPath()), DS);
        $asset->mimetype = mime_content_type($absolute_path);
        $asset->title = $options['title'] ?? null;
        $asset->description = null;
        $asset->modified = FrozenTime::createFromTimestamp($splFileInfo->getMTime());
        $asset->created = $asset->modified;

        $quality = $options['quality'] ?? 90;

        return new ImageAsset($asset, (int)$quality);
    }

    /**
     * Calls the Intervention API's "widen" method.
     * Preserves aspect ratio.
     *
     * @param int $width Width in px
     * @return $this
     */
    public function scaleWidth(int $width)
    {
        $this->trackModification('widen', [$width]);

        return $this;
    }

    /**
     * @return $this
     */
    public function toWebp()
    {
        $this->trackModification('encode', ['webp']);
        $this->format = 'webp';

        return $this;
    }

    /**
     * @return $this
     */
    public function toJpg()
    {
        $this->trackModification('encode', ['jpg']);
        $this->format = 'jpg';

        return $this;
    }

    /**
     * Set CSS classes for __toString() HTML output
     *
     * @param string $css HTML class which will be added on render
     * @return $this
     */
    public function setCSS(string $css)
    {
        $this->css = $css;

        return $this;
    }

    /**
     * @param bool $lazyLoading Control if the image shall be loaded lazily when rendered as Html
     * @return $this
     */
    public function setLazyLoading(bool $lazyLoading = true)
    {
        $this->lazyLoading = $lazyLoading;

        return $this;
    }

    /**
     * Set a custom filename for the modified file.
     *
     * Note: This will override the automatically generated name based on the file's modifications. You should
     * provide a unique name for each modified version.
     *
     * @param string|null $filename the custom filename
     * @return $this
     */
    public function setFilename(?string $filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * e.g.
     * ImageAsset::applyFilter(EpaperFilter::class, ['kombi'])
     * !! Don't pass an ImageManager instance, only string or int properties.
     *
     * @param string $filter ClassName of the Filter
     * @param array $properties Will be passed after the ImageManager instance when calling the Filter's constructor.
     * @return $this
     */
    public function applyFilter(string $filter, array $properties = [])
    {
        $this->trackModification('filter_' . $filter, $properties);

        return $this;
    }

    /**
     * Experimental.
     * Wrapper for Intervention API methods.
     *
     * @param string $method Method name
     * @param mixed ...$params Params for the method
     * @return $this
     * @link https://image.intervention.io/v2
     */
    public function modify(string $method, ...$params)
    {
        $this->trackModification($method, $params);

        return $this;
    }

    /**
     * Get the publicly accessible path (from /webroot)
     *
     * @return string
     * @throws \Nette\Utils\JsonException
     * @throws \Assets\Error\FileNotFoundException
     * @throws \Assets\Error\FilterNotFoundException
     * @throws \Assets\Error\UnkownErrorException
     */
    public function getPath(): string
    {
        $SplFileInfo = $this->getFile();
        if ($SplFileInfo) {
            return $this->getRelativePath($SplFileInfo);
        }

        return $this->render()
            ->getRelativePath();
    }

    /**
     * Renders the ImageAsset as an HTML element.
     *
     * @param array $params Params for the image renderer
     * @return string
     * @throws \Exception
     */
    public function getHTML(array $params = []): string
    {
        $path = $this->getPath();
        $html = new HtmlHelper(new AppView());

        if (!$this->image) {
            $manager = $this->getImageManager();
            $this->image = $manager->make($this->getAbsolutePath());
        }

        $default_params = [
            'alt' => $this->asset->description ?: $this->asset->title,
            'width' => $this->image->width(),
            'height' => $this->image->height(),
            'loading' => $this->lazyLoading ? 'lazy' : 'eager',
            'class' => $this->css,
        ];

        $params = array_merge($default_params, $params);

        return $html->image($path, $params);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function __toString(): string
    {
        return $this->getHTML();
    }

    /**
     * @param string $method Modification method name
     * @param mixed $params Params for that method
     * @param bool $noApi set to true if the modification should not be called in applyModifications().
     * It will just be relevant for the ModificationHash.
     * @return void
     */
    private function trackModification(string $method, $params, bool $noApi = false): void
    {
        if ($noApi) {
            $this->modifications['noApi'][$method] = $params;

            return;
        }

        $this->modifications[$method] = $params;
    }

    /**
     * @return string
     * @throws \Nette\Utils\JsonException
     */
    private function getFilename(): string
    {
        return $this->filename ?: $this->getAssetIdentifier() . '_' . $this->getModificationHash();
    }

    /**
     * @param \SplFileInfo|null $file File instance
     * @return string
     * @throws \Nette\Utils\JsonException
     * @throws \Assets\Error\FileNotFoundException
     */
    private function getRelativePath(?\SplFileInfo $file = null): string
    {
        if ($file) {
            return $this->outputDirectory . $file->getFilename();
        }

        $file = $this->getFile();
        if ($file) {
            return $this->outputDirectory . $file->getFilename();
        }

        if ($this->image) {
            $mimeType = $this->image->mime();
            $format = $this->format ?: Strings::after($mimeType, '/');

            if (!$format) {
                throw new \Exception("Cannot read format or mimetype for modified version of Asset #{$this->asset->id}. ");
            }

            /**
             * Create a new filename.
             */
            return $this->outputDirectory . $this->getFilename() . '.' . $format;
        }

        throw new FileNotFoundException('Cannot get Path for an Image that does not yet exist.
            The render() method must be called before getRelativePath(). ');
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getAbsolutePath(): string
    {
        return WWW_ROOT . ltrim($this->getRelativePath(), DS);
    }

    /**
     * Returns an md5 sum based on the Asset-ID and the modifications.
     *
     * @return string
     * @throws \Nette\Utils\JsonException
     */
    private function getModificationHash(): string
    {
        $modified = $this->asset->modified;
        $timestamp = $modified ? $modified->getTimestamp() : null;

        return md5($this->asset->id . $timestamp . Json::encode($this->modifications));
    }

    /**
     * @return string
     */
    private function getAssetIdentifier(): string
    {
        return $this->asset->id;
    }

    /**
     * @return \SplFileInfo|null
     * @throws \Nette\Utils\JsonException
     */
    private function getFile(): ?\SplFileInfo
    {
        $path = WWW_ROOT . ltrim($this->outputDirectory, DS) . $this->getFilename() . '.' . $this->format;

        if (file_exists($path)) {
            return new \SplFileInfo($path);
        }

        return null;
    }

    /**
     * Renders the image and sets the relative path in $this->image
     *
     * @return $this
     * @throws \Assets\Error\UnkownErrorException
     * @throws \Assets\Error\FilterNotFoundException
     */
    private function render()
    {
        $manager = $this->getImageManager();

        try {
            $image = $manager->make($this->asset->absolute_path);
        } catch (\Exception $e) {
            throw new UnkownErrorException("Could not call ImageManager::make on Asset #{$this->asset->id}. Error: {$e->getMessage()}.");
        }

        $image = $this->applyModifications($image, $manager);
        $this->image = $image;

        FileSystem::createDir(WWW_ROOT . ltrim($this->outputDirectory, DS));

        $image->save($this->getAbsolutePath(), $this->quality, $this->format);

        return $this;
    }

    /**
     * @param \Intervention\Image\Image $image The image instance
     * @param \Intervention\Image\ImageManager $manager The manager instance
     * @return \Intervention\Image\Image
     * @throws \Assets\Error\FilterNotFoundException
     */
    private function applyModifications(Image $image, ImageManager $manager): Image
    {
        $modifications = $this->modifications;
        unset($modifications['noApi']);

        foreach ($modifications as $method => $params) {
            if (Strings::contains($method, 'filter_')) {
                $filterClassName = Strings::after($method, 'filter_') ?? '';
                if (!class_exists($filterClassName)) {
                    throw new FilterNotFoundException("Filter {$filterClassName} does not exist. ");
                }

                /**
                 * @var \Intervention\Image\Filters\FilterInterface $filter
                 */
                $filter = new $filterClassName($manager, ...$params);
                $image = $filter->applyFilter($image);
                continue;
            }

            $params = is_array($params) ? $params : [$params];
            $image->{$method}(...$params);
        }

        return $image;
    }

    /**
     * @return \Intervention\Image\ImageManager
     */
    private function getImageManager(): ImageManager
    {
        $driver = Configure::read('AssetsPlugin.ImageAsset.driver', 'gd');

        return new ImageManager([
            'driver' => $driver,
        ]);
    }
}
