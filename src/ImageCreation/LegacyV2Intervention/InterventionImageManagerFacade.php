<?php

declare(strict_types=1);

namespace Assets\ImageCreation\LegacyV2Intervention;

use Assets\ImageCreation\ImageInterface;
use Assets\ImageCreation\ImageManagerInterface;
use Intervention\Image\ImageManager;

final class InterventionImageManagerFacade implements ImageManagerInterface
{
    public function __construct(
        private ImageManager $interventionImageManager,
    ) {
    }

    public function read(string $absolutePath): ImageInterface
    {
        return new InterventionImageFacade(
            $this->interventionImageManager->make($absolutePath),
        );
    }

    public function create(int $width, int $height): ImageInterface
    {
        return new InterventionImageFacade(
            $this->interventionImageManager->canvas($width, $height),
        );
    }

    public function canvas(int $width, int $height): ImageInterface
    {
        return $this->create($width, $height);
    }
}
