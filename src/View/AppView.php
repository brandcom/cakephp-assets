<?php

namespace Assets\View;

use Assets\View\Helper\PictureHelper;
use Assets\View\Helper\TextAssetPreviewHelper;
use Cake\View\View;

/**
 * @property TextAssetPreviewHelper $TextAssetPreview
 * @property PictureHelper $Picture
 */
class AppView extends View
{
    public function initialize(): void
    {
        parent::initialize();
    }
}