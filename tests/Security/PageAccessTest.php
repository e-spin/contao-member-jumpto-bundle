<?php

declare(strict_types=1);

namespace Espin\MemberJumpToBundle\Test\Security;

use Espin\MemberJumpToBundle\Security\PageAccess;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Espin\MemberJumpToBundle\Security\PageAccess
 */
class PageAccessTest extends TestCase
{
    public function testOpenPageIsAlwaysGranted(): void
    {
        self::assertTrue(PageAccess::isGranted(false, [], []));
    }

    public function testProtectedPageNeedsACommonGroup(): void
    {
        self::assertTrue(PageAccess::isGranted(true, ['2', '3'], [3, 9]));
        self::assertFalse(PageAccess::isGranted(true, ['2', '3'], [4]));
    }

    public function testProtectedPageWithoutGroupsIsDenied(): void
    {
        self::assertFalse(PageAccess::isGranted(true, [], [1]));
    }
}
