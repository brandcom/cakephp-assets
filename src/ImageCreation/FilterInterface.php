<?php

declare(strict_types=1);

namespace Assets\ImageCreation;

interface FilterInterface
{
    public function applyFilter(ImageInterface $image): ImageInterface;
}
