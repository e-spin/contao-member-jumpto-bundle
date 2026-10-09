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

namespace Espin\MemberJumpToBundle\Model;

/**
 * One possible target page of a module.
 */
final class JumpToEntry
{
    public function __construct(
        public readonly int $pageId,
        public readonly string $label,
        public readonly bool $default
    ) {
    }
}
