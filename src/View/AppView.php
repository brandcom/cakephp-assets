<?php

namespace Assets\View;

use Assets\View\Helper\TextAssetPreviewHelper;
use Cake\View\View;

/**
 * @property TextAssetPreviewHelper $TextAssetPreview
 */
class AppView extends View
{
    public function initialize(): void
    {
        parent::initialize();
    }
}