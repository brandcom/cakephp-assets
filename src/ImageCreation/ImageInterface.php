<?php

declare(strict_types=1);

namespace Assets\ImageCreation;

interface ImageInterface
{
    public function width(): int;

    public function height(): int;

    public function mime(): string;

    public function save(string $absolutePath, ?int $quality, ?string $format): void;
}
