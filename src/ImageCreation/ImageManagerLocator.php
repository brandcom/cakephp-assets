<?php

declare(strict_types=1);

namespace Assets\ImageCreation;

use Assets\ImageCreation\Intervention\InterventionImageManagerFacade;
use Cake\Core\Configure;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;

final class ImageManagerLocator
{
    private static ?ImageManagerInterface $manager = null;

    public static function getImageManager(): ImageManagerInterface
    {
        if (self::$manager !== null) {
            return self::$manager;
        }

        $driver = Configure::read('AssetsPlugin.ImageAsset.driver', 'gd');

        $manager = self::$manager = new InterventionImageManagerFacade(
            match ($driver) {
                'imagick',
                ImagickDriver::class => ImageManager::imagick(
                    options: Configure::read('AssetsPlugin.ImageAsset.imagickOptions', []),
                ),
                'gd',
                GdDriver::class => ImageManager::gd(
                    options: Configure::read('AssetsPlugin.ImageAsset.gdOptions', []),
                ),
                default => throw new \LogicException('no driver configured'),
            },
        );

        return $manager;
    }

    public static function setImageManager(ImageManagerInterface $manager): void
    {
        self::$manager = $manager;
    }
}
