<?php

declare(strict_types=1);

namespace Assets\ImageCreation\Intervention;

use Assets\Error\InvalidArgumentException;
use Assets\ImageCreation\ImageInterface;
use Intervention\Image\MediaType;
use Intervention\Image\Interfaces as Intervention;

final class InterventionImageFacade implements ImageInterface
{
    /**
     * @param array<string, string> $legacyMdifiersMap
     */
    public function __construct(
        private Intervention\ImageInterface $interventionImage,
        private array                       $legacyMdifiersMap,
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

    public function modify(string $modifier, array $params): ImageInterface
    {
        if (method_exists($this->interventionImage, $modifier)) {
            $this->interventionImage->{$modifier}(...$params);
            return $this;
        }

        $mappedFromLegacy = $this->legacyMdifiersMap[$modifier] ?? null;

        if (
            $mappedFromLegacy !== null
            && method_exists($this->interventionImage, $mappedFromLegacy)
        ) {
            $this->interventionImage->{$mappedFromLegacy}(...$params);
            return $this;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Modifier `%s` does not exist',
                $modifier,
            ),
        );
    }
}
