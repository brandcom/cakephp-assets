<?php

declare(strict_types=1);

namespace Assets\ImageCreation\Intervention;

final class LegacySupport
{
    /**
     * @see https://image.intervention.io/v3/getting-started/upgrade
     *
     * Note: Signature might have changed.
     */
    public const V2_TO_V3_MODIFIERS_MAP = [
        'canvas'     => 'create',
        'circle'     => 'drawCircle',
        'ellipse'    => 'drawEllipse',
        'line'       => 'drawLine',
        'pixel'      => 'drawPixel',
        'filter'     => 'modify',
        'insert'     => 'place',
        'make'       => 'read',
        'mime'       => 'encodedImage',
        'polygon'    => 'drawPolygon',
        'rectangle'  => 'drawRectangle',
        'limitColors'=> 'reduceColors',
        'getCore'    => 'core',
        'orientate'  => 'orient',
        'widen'      => 'scale',
        'heighten'   => 'scale',
        'fit'        => 'cover',
    ];
}
