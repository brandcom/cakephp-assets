<?php

declare(strict_types=1);

namespace Assets\ImageCreation\Intervention;

use Assets\Error\InvalidArgumentException;
use Assets\Error\ModificationFailedException;
use Assets\ImageCreation\ImageInterface;
use Intervention\Image\MediaType;
use Intervention\Image\Interfaces as Intervention;

final class InterventionImageFacade implements ImageInterface
{
    /**
     * @param array<string, string|callable> $legacyMdifiersMap
     */
    public function __construct(
        private Intervention\ImageInterface $interventionImage,
        private array                       $legacyMdifiersMap,
    )
    {
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
        if ($params === []) {
            throw new ModificationFailedException('Empty params given');
        }

        $modify = function (string $modifier, array $params, ?string $legacyModifier = null) {
            try {
                $this->interventionImage->{$modifier}(...$params);
            } catch (\Throwable $throwable) {

                if ($legacyModifier === null) {
                    $callback = $this->legacyMdifiersMap[$modifier] ?? null;
                    if ($callback !== null && is_callable($callback)) {
                        $callback($this->interventionImage, ...$params);
                    }
                }

                throw new ModificationFailedException(
                    sprintf(
                        'Modification `%s` failed with params: %s.%s',
                        $modifier,
                        var_export($params, true),
                        $legacyModifier !== null ? ' Legacy: ' . $legacyModifier : '',
                    ),
                    previous: $throwable,
                );
            }
        };

        if (method_exists($this->interventionImage, $modifier)) {
            $modify($modifier, $params);
            return $this;
        }

        $mappedFromLegacy = $this->legacyMdifiersMap[$modifier] ?? null;

        if (
            $mappedFromLegacy !== null
            && method_exists($this->interventionImage, $mappedFromLegacy)
        ) {
            $modify($mappedFromLegacy, $params, $modifier);
            return $this;
        }

        throw new ModificationFailedException(sprintf('Modifier `%s` does not exist', $modifier));
    }
}
