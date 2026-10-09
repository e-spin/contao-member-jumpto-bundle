<?php

declare(strict_types=1);

namespace Espin\MemberJumpToBundle\Test\Model;

use Espin\MemberJumpToBundle\Model\JumpToList;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Espin\MemberJumpToBundle\Model\JumpToList
 * @covers \Espin\MemberJumpToBundle\Model\JumpToEntry
 */
class JumpToListTest extends TestCase
{
    public function testReadsSerializedRows(): void
    {
        $list = JumpToList::fromStored(\serialize([
            ['page' => '4', 'label' => ' Account ', 'default' => ''],
            ['page' => '7', 'label' => '', 'default' => '1'],
        ]));

        self::assertCount(2, $list->entries());
        self::assertSame('Account', $list->find(4)?->label);
        self::assertSame(7, $list->default()?->pageId);
    }

    public function testFirstEntryIsDefaultWithoutMark(): void
    {
        $list = JumpToList::fromStored([['page' => 3], ['page' => 5]]);

        self::assertSame(3, $list->default()?->pageId);
    }

    public function testFirstMarkWinsWithSeveralMarks(): void
    {
        $list = JumpToList::fromStored([
            ['page' => 3],
            ['page' => 5, 'default' => '1'],
            ['page' => 6, 'default' => '1'],
        ]);

        self::assertSame(5, $list->default()?->pageId);
        self::assertFalse($list->find(6)?->default);
    }

    public function testSkipsBrokenAndDuplicateRows(): void
    {
        $list = JumpToList::fromStored([['page' => 0], 'foo', ['page' => 2], ['page' => 2, 'label' => 'again']]);

        self::assertCount(1, $list->entries());
        self::assertSame('', $list->find(2)?->label);
    }

    /**
     * @dataProvider provideUnusable
     */
    public function testUnusableValuesGiveAnEmptyList(mixed $stored): void
    {
        $list = JumpToList::fromStored($stored);

        self::assertTrue($list->isEmpty());
        self::assertNull($list->default());
        self::assertNull($list->find(1));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function provideUnusable(): iterable
    {
        yield 'null' => [null];
        yield 'empty string' => [''];
        yield 'garbage' => ['a:1:{broken'];
        yield 'object' => [\serialize(new \stdClass())];
    }
}
