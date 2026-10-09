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

namespace Espin\MemberJumpToBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Espin\MemberJumpToBundle\EspinMemberJumpToBundle;
use MenAtWork\MultiColumnWizardBundle\MultiColumnWizardBundle;

/**
 * @internal
 */
final class Plugin implements BundlePluginInterface
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[\Override]
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(EspinMemberJumpToBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, MultiColumnWizardBundle::class]),
        ];
    }
}
