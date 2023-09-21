<?php
declare(strict_types=1);

namespace Assets\Test\integration\Nette;

use Cake\TestSuite\TestCase;
use Nette\Utils\FileSystem;

class FileSystemTest extends TestCase
{
    protected const FILE_PATH = __DIR__ . DS . 'example-file.txt';

    public function testFindFiles(): void
    {
        $content = FileSystem::read(static::FILE_PATH);
        static::assertEquals('This is a file.', trim($content));

        $copyPath = static::FILE_PATH . '.copy';
        FileSystem::copy(static::FILE_PATH, $copyPath);
        static::assertFileExists($copyPath);

        FileSystem::delete($copyPath);
        static::assertFileDoesNotExist($copyPath);
    }
}
