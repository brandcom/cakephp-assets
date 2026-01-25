<?php

declare(strict_types=1);

namespace Assets\ImageCreation\Intervention;

use Assets\ImageCreation\ImageInterface;
use Assets\ImageCreation\ImageManagerInterface;
use Intervention\Image\Interfaces as Intervention;

final class InterventionImageManagerFacade implements ImageManagerInterface
{
    public function __construct(
        private Intervention\ImageManagerInterface $interventionImageManager,
    ) {
    }

    public function read(string $absolutePath): ImageInterface
    {
        return new InterventionImageFacade(
            $this->interventionImageManager->read($absolutePath),
        );
    }
}
