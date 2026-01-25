<?php

declare(strict_types=1);

namespace Assets\ImageCreation;

use Assets\ImageCreation\Intervention\InterventionImageManagerFacade;
use Assets\ImageCreation\Intervention\LegacySupport;
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
        $legacyModifiersMap = Configure::read('AssetsPlugin.ImageAsset.legacyModifiersMap');

        return self::$manager = new InterventionImageManagerFacade(
            match ($driver) {
                'imagick',
                ImagickDriver::class => ImageManager::imagick(
                    ...Configure::read('AssetsPlugin.ImageAsset.imagickOptions', []),
                ),
                'gd',
                GdDriver::class => ImageManager::gd(
                    ...Configure::read('AssetsPlugin.ImageAsset.gdOptions', []),
                ),
                default => throw new \LogicException('no driver configured'),
            },
            legacyModifiersMap: $legacyModifiersMap !== null
                ? $legacyModifiersMap
                : LegacySupport::V2_TO_V3_MODIFIERS_MAP,
        );
    }

    public static function setImageManager(ImageManagerInterface $manager): void
    {
        self::$manager = $manager;
    }
}
