<?php
declare(strict_types=1);

namespace Assets\Test\integration\Nette;

use Cake\TestSuite\TestCase;
use Nette\Utils\Strings;

class StringsTest extends TestCase
{
    public function testStrings(): void
    {
        $string = 'some_string-with/random7stuff.here';

        $some_string = Strings::before($string, '-');
        static::assertEquals('some_string', $some_string);

        $here = Strings::after($string, '.', -1);
        static::assertEquals('here', $here);

        $random = Strings::before(Strings::after($string, '/'), '7');
        static::assertEquals('random', $random);

        $some_string = Strings::substring($string, 0, 11);
        static::assertEquals('some_string', $some_string);
        $ome_str = Strings::substring($string, 1, 7);
        static::assertEquals('ome_str', $ome_str);

        $someDotDotDot = Strings::truncate($string, 5);
        static::assertEquals("some\u{2026}", $someDotDotDot);
    }
}
