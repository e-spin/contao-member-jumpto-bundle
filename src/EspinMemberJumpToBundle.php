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

namespace Espin\MemberJumpToBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @final
 *
 * @psalm-suppress DeprecatedInterface AbstractBundle is no drop-in replacement: it disables the
 *     classic Extension auto-discovery, so switching would require porting the extension to
 *     loadExtension().
 */
class EspinMemberJumpToBundle extends Bundle
{
    #[\Override]
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
