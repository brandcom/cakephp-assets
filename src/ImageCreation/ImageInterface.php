<?php

declare(strict_types=1);

namespace Assets\ImageCreation;

use Assets\Error\ModificationFailedException;

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
}
