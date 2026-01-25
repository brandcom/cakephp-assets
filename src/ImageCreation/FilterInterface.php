<?php

declare(strict_types=1);

namespace Assets\ImageCreation;

interface FilterInterface
{
    public static function create(ImageManagerInterface $manager, mixed ...$params): FilterInterface;

    public function applyFilter(ImageInterface $image): ImageInterface;
}
