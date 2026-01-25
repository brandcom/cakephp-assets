<?php

declare(strict_types=1);

namespace Assets\ImageCreation\Intervention;

use Assets\ImageCreation\ImageInterface;
use Assets\ImageCreation\ImageManagerInterface;
use Intervention\Image\Interfaces as Intervention;

final class InterventionImageManagerFacade implements ImageManagerInterface
{
    /**
     * @param array<string, string|callable> $legacyModifiersMap
     */
    public function __construct(
        private Intervention\ImageManagerInterface $interventionImageManager,
        private array $legacyModifiersMap,
    ) {
    }

    public function read(string $absolutePath): ImageInterface
    {
        return new InterventionImageFacade(
            $this->interventionImageManager->read($absolutePath),
            $this->legacyModifiersMap,
        );
    }

    public function create(int $width, int $height): ImageInterface
    {
        return new InterventionImageFacade(
            $this->interventionImageManager->create($width, $height),
            $this->legacyModifiersMap,
        );
    }

    public function canvas(int $width, int $height): ImageInterface
    {
        return $this->create($width, $height);
    }
}
