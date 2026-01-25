<?php

declare(strict_types=1);

namespace Assets\ImageCreation\Intervention;

use Assets\ImageCreation\ImageInterface;
use Intervention\Image\MediaType;
use Intervention\Image\Interfaces as Intervention;

final class InterventionImageFacade implements ImageInterface
{
    public function __construct(
        private Intervention\ImageInterface $interventionImage
    ) {
    }

    public function width(): int
    {
        return $this->interventionImage->width();
    }

    public function height(): int
    {
        return $this->interventionImage->height();
    }

    /**
     * @see MediaType
     */
    public function mime(): string
    {
        return $this->interventionImage->origin()->mediaType();
    }

    public function save(string $absolutePath, ?int $quality, ?string $format): void
    {
        if ($format !== null) {
            $absolutePath .= $absolutePath . '.' . $format;
        }

        $this->interventionImage->save($absolutePath, quality: $quality);
    }
}
