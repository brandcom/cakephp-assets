<?php

declare(strict_types=1);

namespace Assets\ImageCreation\Intervention;

use Assets\Error\ModificationFailedException;
use Intervention\Image\Image;

final class LegacySupport
{
    /**
     * @see https://image.intervention.io/v3/getting-started/upgrade
     *
     * Note: Signature might have changed.
     */
    public const V2_TO_V3_MODIFIERS_MAP = [
        // Name changed
        'canvas' => 'create',
        'circle' => 'drawCircle',
        'ellipse' => 'drawEllipse',
        'line' => 'drawLine',
        'pixel' => 'drawPixel',
        'filter' => 'modify',
        'insert' => 'place',
        'make' => 'read',
        'mime' => 'encodedImage',
        'polygon' => 'drawPolygon',
        'rectangle' => 'drawRectangle',
        'limitColors' => 'reduceColors',
        'getCore' => 'core',
        'orientate' => 'orient',
        'widen' => 'scale',
        'heighten' => 'scale',
        'fit' => 'cover',
        // Signature changed
        'crop' => [self::class, 'legacyCrop'],
        'encode' => [self::class, 'legacyEncode'],
        'exif' => [self::class, 'legacyExif'],
        'fill' => [self::class, 'legacyFill'],
        'flip' => [self::class, 'legacyFlip'],
        'text' => [self::class, 'legacyText'],
        'resizeCanvas' => [self::class, 'legacyResizeCanvas'],
        'trim' => [self::class, 'legacyTrim'],
        'resize' => [self::class, 'legacyResize'],
    ];

    public static function legacyCrop(Image $image, mixed ...$params): Image
    {
        throw new ModificationFailedException('legacy fallback for crop not implemented');
    }

    public static function legacyEncode(Image $image, mixed ...$params): Image
    {
        throw new ModificationFailedException('legacy fallback for encode not implemented');
    }

    public static function legacyExif(Image $image, mixed ...$params): Image
    {
        throw new ModificationFailedException('legacy fallback for exif not implemented');
    }

    public static function legacyFill(Image $image, mixed ...$params): Image
    {
        throw new ModificationFailedException('legacy fallback for fill not implemented');
    }

    public static function legacyFlip(Image $image, mixed ...$params): Image
    {
        throw new ModificationFailedException('legacy fallback for flip not implemented');
    }

    public static function legacyText(Image $image, mixed ...$params): Image
    {
        throw new ModificationFailedException('legacy fallback for text not implemented');
    }

    public static function legacyResizeCanvas(Image $image, mixed ...$params): Image
    {
        throw new ModificationFailedException('legacy fallback for resizeCanvas not implemented');
    }

    public static function legacyTrim(Image $image, mixed ...$params): Image
    {
        throw new ModificationFailedException('legacy fallback for trim not implemented');
    }

    public static function legacyResize(Image $image, mixed ...$params): Image
    {
        throw new ModificationFailedException('legacy fallback for resize not implemented');
    }
}
