<?php

namespace Assets\Utilities;

use Assets\Model\Entity\Asset;
use Assets\View\AppView;
use Cake\Core\Configure;
use Cake\View\Helper\HtmlHelper;
use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use const DS;
use const WWW_ROOT;

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

    private string $css;

    private string $outputDirectory;

    public function __construct(Asset $asset, int $quality = 90)
    {
        $this->asset = $asset;
        $this->quality = $quality;
        $this->modifications = [];
        $this->image = null;
        $this->format = null;
        $this->css = "image-asset";
        $this->outputDirectory = Configure::read("AssetsPlugin.ImageAsset.outDir");

        $this->trackModification('constructor', ['quality' => $quality], true);
    }

    /**
     * Calls the Intervention API's "widen" method.
     * Preserves aspect ratio.
     */
    public function scaleWidth(int $width): ImageAsset
    {
        $this->trackModification('widen', [$width]);
        return $this;
    }

    public function toWebp(): ImageAsset
    {
        $this->trackModification('encode', ['webp']);
        $this->format = 'webp';
        return $this;
    }

    public function toJpg(): ImageAsset
    {
        $this->trackModification('encode', ['jpg']);
        $this->format = 'jpg';
        return $this;
    }

    /**
     * Set CSS classes for __toString() HTML output
     */
    public function setCSS(string $css): ImageAsset
    {
        $this->css = $css;
        return $this;
    }

    /**
     * e.g.
     * ImageAsset::applyFilter(EpaperFilter::class, ['kombi'])
     *
     * $filer is the className (string) of the Filter.
     * $properties will be passed after the ImageManager instance
     * when calling the Filter's constructor.
     *
     * !! Don't pass an ImageManager instance, only string or int properties.
     */
    public function applyFilter(string $filter, array $properties = []): ImageAsset
    {
        $this->trackModification('filter_' . $filter, $properties);
        return $this;
    }

    /**
     * Experimental.
     * Wrapper for Intervention API methods.
     *
     * @link https://image.intervention.io/v2
     */
    public function modify(string $method, ...$params): ImageAsset
    {
        $this->trackModification($method, $params);
        return $this;
    }

    /**
     * Get the publicly accessible path (from /webroot)
     */
    public function getPath(): string
    {
        $SplFileInfo = $this->getFile();
        if ($SplFileInfo) {
            return $this->getRelativePath($SplFileInfo);
        }

        if ($this->render()) {
            return $this->getRelativePath();
        }

        throw new \Exception("Cannot get path for ImageAsset for Asset #{$this->asset->id}");
    }

    /**
     * Renders the ImageAsset as a HTML element.
     */
    public function getHTML(): string
    {
        $path = $this->getPath();
        $html = new HtmlHelper(new AppView());

        if (!$this->image) {
            $manager = $this->getImageManager();
            $this->image = $manager->make($this->getAbsolutePath());
        }

        return $html->image($path, [
            'alt' => $this->asset->description ?: $this->asset->title,
            'width' => $this->image->width(),
            'loading' => 'lazy',
            'height' => $this->image->height(),
            'class' => $this->css,
        ]);
    }

    public function __toString(): string
    {
        return $this->getHTML();
    }

    /**
     * set $noApi to true if the modification should not be called in
     * applyModifications(). It will just be relevant for the ModificationHash.
     */
    private function trackModification(string $method, $params, bool $noApi = false): void
    {
        if ($noApi) {
            $this->modifications['noApi'][$method] = $params;
            return;
        }

        $this->modifications[$method] = $params;
    }

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
            return $this->outputDirectory . $this->getAssetIdentifier() . '_' . $this->getModificationHash() . '.' . $format;
        }

        throw new \Exception("Cannot get Path for an Image that does not yet exist. The render() method must be called before getRelativePath(). ");
    }

    private function getAbsolutePath(): string
    {
        return WWW_ROOT . ltrim($this->getRelativePath(), DS);
    }

    /**
     * Returns an md5 sum based on the Asset-ID and the modifications.
     */
    private function getModificationHash(): string
    {
        $modified = $this->asset->modified;
        $timestamp = $modified ? $modified->getTimestamp() : null;

        return md5($this->asset->id . $timestamp . Json::encode($this->modifications));
    }

    private function getAssetIdentifier(): string
    {
        return $this->asset->id;
    }

    private function getFile(): ?\SplFileInfo
    {
        if (!is_dir(WWW_ROOT . ltrim($this->outputDirectory, DS))) {
            return null;
        }

        $files = Finder::findFiles($this->getAssetIdentifier() . '_' . $this->getModificationHash() . '*')
            ->in(WWW_ROOT . ltrim($this->outputDirectory, DS));

        foreach ($files as $path => $SplFileInfo) {

            return $SplFileInfo;
        }

        return null;
    }

    /**
     * Renders the Image and returns the
     * relative path
     */
    private function render(): bool
    {
        $manager = $this->getImageManager();

        try {
            $image = $manager->make($this->asset->absolute_path);
        } catch (\Exception $e) {
            throw new \Exception("Could not call ImageManager::make on Asset #{$this->asset->id}. Error: {$e->getMessage()}.");
        }

        $image = $this->applyModifications($image, $manager);
        $this->image = $image;

        FileSystem::createDir(WWW_ROOT . ltrim($this->outputDirectory, DS));

        $image->save($this->getAbsolutePath(), $this->quality, $this->format);

        return true;
    }

    private function applyModifications(Image $image, ImageManager $manager): Image
    {
        $modifications = $this->modifications;
        unset($modifications['noApi']);

        foreach ($modifications as $method => $params) {

            if (Strings::contains($method, 'filter_')) {
                $filterClassName = Strings::after($method, 'filter_') ?? '';
                if (!class_exists($filterClassName)) {
                    throw new \Exception("Filter {$filterClassName} does not exist. ");
                }

                /**
                 * @var FilterInterface $filter
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

    private function getImageManager(): ImageManager
    {
        $driver = Configure::read('AssetsPlugin.ImageAsset.driver');

        return new ImageManager([
            'driver' => $driver,
        ]);
    }
}
