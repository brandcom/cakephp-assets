<?php

declare(strict_types=1);

namespace Assets\ImageCreation;

interface ImageManagerInterface
{
    public function read(string $absolutePath): ImageInterface;
}
