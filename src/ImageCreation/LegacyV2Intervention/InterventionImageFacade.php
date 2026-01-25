<?php

declare(strict_types=1);

namespace Assets\ImageCreation\LegacyV2Intervention;

use Assets\Error\ModificationFailedException;
use Assets\ImageCreation\ImageInterface;
use Intervention\Image\Image;
use Intervention\Image\MediaType;
use Nette\Utils\FileSystem;

final class InterventionImageFacade implements ImageInterface
{
    public function __construct(
        private Image $interventionImage,
    )  {
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
        if ($format !== null && !str_ends_with($absolutePath, '.' . $format)) {
            $absolutePath .= '.' . $format;
        }

        $dir = dirname($absolutePath);

        if (!is_dir($dir)) {
            FileSystem::createDir($dir);
        }

        $this->interventionImage->save($absolutePath, quality: $quality);
    }

    public function modify(string $modifier, array $params): ImageInterface
    {
        if ($params === []) {
            throw new ModificationFailedException('Empty params given');
        }

        try {
            if (method_exists($this->interventionImage, $modifier)) {
                $this->interventionImage->{$modifier}(...$params);
            }
        } catch (\Throwable $throwable) {
            throw new ModificationFailedException(
                sprintf(
                    'Modification `%s` failed with params: %s.',
                    $modifier,
                    var_export($params, true),
                ),
                previous: $throwable,
            );
        }

        throw new ModificationFailedException(sprintf('Modifier `%s` does not exist', $modifier));
    }

    public function getInterventionImage(): Image
    {
        return $this->interventionImage;
    }

    public function requireInterventionImage(): Image
    {
        return $this->interventionImage;
    }
}
