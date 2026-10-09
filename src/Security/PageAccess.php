<?php

/**
 * This file is part of e-spin/contao-member-jumpto-bundle.
 *
 * (c) 2026 e-spin
 *
 * @package   e-spin/contao-member-jumpto-bundle
 * @author    Ingolf Steinhardt <info@e-spin.de>
 * @copyright 2026 e-spin
 * @license   LGPL-3.0-or-later
 */

declare(strict_types=1);

namespace Espin\MemberJumpToBundle\Security;

/**
 * The frontend access rule of Contao for protected pages.
 */
final class PageAccess
{
    /**
     * @param array<array-key, mixed> $pageGroups   The groups which are allowed to see the page.
     * @param array<array-key, mixed> $memberGroups The groups of the member.
     */
    public static function isGranted(bool $protected, array $pageGroups, array $memberGroups): bool
    {
        if (!$protected) {
            return true;
        }

        // A protected page without groups is not accessible, the same way as it is in the Contao core.
        return [] !== \array_intersect(\array_map('intval', $pageGroups), \array_map('intval', $memberGroups));
    }
}
