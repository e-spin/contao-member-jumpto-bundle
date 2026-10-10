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
        self::assertSame('Account', $list->entries()[0]->label);
        self::assertSame(7, self::defaultPage($list));
    }

    public function testFirstEntryIsDefaultWithoutMark(): void
    {
        $list = JumpToList::fromStored([['page' => 3], ['page' => 5]]);

        self::assertSame(3, self::defaultPage($list));
    }

    public function testFirstMarkWinsWithSeveralMarks(): void
    {
        $list = JumpToList::fromStored([
            ['page' => 3],
            ['page' => 5, 'default' => '1'],
            ['page' => 6, 'default' => '1'],
        ]);

        self::assertSame(5, self::defaultPage($list));
        self::assertFalse($list->entries()[2]->default);
    }

    public function testSkipsBrokenRows(): void
    {
        $list = JumpToList::fromStored([['page' => 0], 'foo', ['page' => 2]]);

        self::assertCount(1, $list->entries());
        self::assertSame(2, self::defaultPage($list));
    }

    public function testKeepsThePageSeveralTimes(): void
    {
        $list = JumpToList::fromStored([
            ['page' => 2, 'label' => 'first'],
            ['page' => 5],
            ['page' => 2, 'label' => 'again', 'default' => '1'],
        ]);

        self::assertCount(3, $list->entries());
        self::assertSame('first', $list->entries()[0]->label);
        self::assertSame('again', $list->entries()[2]->label);
        self::assertTrue($list->entries()[2]->default);
        self::assertFalse($list->entries()[0]->default);
    }

    public function testStoredValueIsReadBackUnchanged(): void
    {
        $list = JumpToList::fromStored([['page' => 3, 'label' => 'A'], ['page' => 5, 'default' => '1']]);

        self::assertEquals($list, JumpToList::fromStored($list->toStored()));
    }

    private static function defaultPage(JumpToList $list): ?int
    {
        foreach ($list->entries() as $entry) {
            if ($entry->default) {
                return $entry->pageId;
            }
        }

        return null;
    }

    /**
     * @dataProvider provideUnusable
     */
    public function testUnusableValuesGiveAnEmptyList(mixed $stored): void
    {
        $list = JumpToList::fromStored($stored);

        self::assertSame([], $list->entries());
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
