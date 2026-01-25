<?php

declare(strict_types=1);

namespace Assets\ImageCreation;

use Assets\Error\ModificationFailedException;
use Intervention\Image\Interfaces as Intervention;
use RuntimeException;

interface ImageInterface
{
    public function width(): int;

    public function height(): int;

    public function mime(): string;

    public function save(string $absolutePath, ?int $quality, ?string $format): void;

    /**
     * Modify the image based on the underlying implementation.
     * @throws ModificationFailedException
     */
    public function modify(string $modifier, array $params): self;

    public function getInterventionImage(): ?Intervention\ImageInterface;

    /**
     * @throws RuntimeException
     */
    public function requireInterventionImage(): Intervention\ImageInterface;
}
