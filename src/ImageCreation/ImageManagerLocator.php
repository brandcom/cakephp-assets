<?php

declare(strict_types=1);

namespace Assets\ImageCreation;

use Assets\ImageCreation\Intervention\InterventionImageManagerFacade;
use Assets\ImageCreation\LegacyV2Intervention as V2;
use Assets\ImageCreation\Intervention\LegacySupport;
use Cake\Core\Configure;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use LogicException;

final class ImageManagerLocator
{
    private static ?ImageManagerInterface $manager = null;

    public static function getImageManager(): ImageManagerInterface
    {
        if (self::$manager !== null) {
            return self::$manager;
        }

        if (interface_exists('\Intervention\Image\Interfaces\ImageInterface')) {
            return self::$manager = self::createV3ManagerFacade();
        }

        if (class_exists('\Intervention\Image\ImageManager')) {
            return self::$manager = self::createV2ManagerFacade();
        }

        throw new LogicException(
            'intervention/image must be installed in v2 or v3',
        );
    }

    public static function setImageManager(ImageManagerInterface $manager): void
    {
        self::$manager = $manager;
    }

    private static function createV2ManagerFacade(): ImageManagerInterface
    {
        $driver = Configure::read('AssetsPlugin.ImageAsset.driver', 'gd');

        return new V2\InterventionImageManagerFacade(
            new ImageManager(['driver' => $driver]),
            [],
        );
    }

    private static function createV3ManagerFacade(): ImageManagerInterface
    {
        $driver = Configure::read('AssetsPlugin.ImageAsset.driver', 'gd');
        $legacyModifiersMap = Configure::read('AssetsPlugin.ImageAsset.legacyModifiersMap');

        return new InterventionImageManagerFacade(
            match ($driver) {
                'imagick',
                ImagickDriver::class => ImageManager::imagick(
                    ...Configure::read('AssetsPlugin.ImageAsset.imagickOptions', []),
                ),
                'gd',
                GdDriver::class => ImageManager::gd(
                    ...Configure::read('AssetsPlugin.ImageAsset.gdOptions', []),
                ),
                default => throw new LogicException('no driver configured'),
            },
            legacyModifiersMap: $legacyModifiersMap !== null
                ? $legacyModifiersMap
                : LegacySupport::V2_TO_V3_MODIFIERS_MAP,
        );
    }
}
