<?php

declare(strict_types=1);

namespace Assets\ImageCreation;

use Assets\ImageCreation\Intervention\InterventionImageFacade;

interface ImageManagerInterface
{
    public function read(string $absolutePath): ImageInterface;

    public function create(int $width, int $height): ImageInterface;

    /**
     * @deprecated use {@see self::create()}
     */
    public function canvas(int $width, int $height): ImageInterface;
}
