<?php
declare(strict_types=1);

namespace Assets\Test\Utilities;

use Assets\Utilities\ImageAsset;
use Cake\TestSuite\TestCase;

class ImageAssetTest extends TestCase
{
    protected const DATA_IMAGES = __DIR__ . DS;

    protected function setUp(): void
    {
        parent::setUp();
        if (!defined('PLUGIN_ROOT')) {
            include __DIR__ . DS . '..' . DS . 'bootstrap.php';
        }
    }

    public function testCreateFromPathWithInvalidPath(): void
    {
        static::expectException(\InvalidArgumentException::class);
        $image = ImageAsset::createFromPath('invalid-path');
    }

    public function testCreateFromPathWithValidPath(): void
    {
        $path = static::DATA_IMAGES . 'cake.jpg';
        $image = ImageAsset::createFromPath($path);
        static::assertInstanceOf(ImageAsset::class, $image);

        $renderedPath = WWW_ROOT . $image->toWebp()->scaleWidth(120)->getPath();
        static::assertFileExists($renderedPath);
        $splFileInfo = new \SplFileInfo($renderedPath);
        static::assertEquals('image/webp', mime_content_type($renderedPath));
        static::assertEquals(6178, $splFileInfo->getSize());

        $renderedPathOfLargeJpg = WWW_ROOT . $image->toJpg()->scaleWidth(240)->getPath();
        static::assertFileExists($renderedPathOfLargeJpg);
        $splFileInfoJpg = new \SplFileInfo($renderedPathOfLargeJpg);
        static::assertEquals('image/jpeg', mime_content_type($renderedPathOfLargeJpg));
        static::assertEquals(22336, $splFileInfoJpg->getSize());
    }

    public function testHtmlCreation(): void
    {
        $path = static::DATA_IMAGES . 'cake.jpg';
        $image = ImageAsset::createFromPath($path);
        static::assertInstanceOf(ImageAsset::class, $image);

        $html = $image
            ->scaleWidth(230)
            ->setCSS('some-css')
            ->setLazyLoading(false)
            ->getHTML([
                'data-test' => 123,
            ]);

        static::assertNotEmpty($html);
        static::assertStringContainsString('width="230"', $html);
        static::assertStringContainsString('class="some-css"', $html);
        static::assertStringContainsString('data-test="123"', $html);
        static::assertStringContainsString('loading="eager"', $html);
    }
}
