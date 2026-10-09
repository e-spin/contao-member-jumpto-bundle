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

use Contao\FrontendUser;
use Contao\PageModel;
use Contao\StringUtil;

/**
 * Finds the page a member is allowed to land on.
 */
class TargetPageResolver
{
    /**
     * @return PageModel|null Null if the page is gone, not published or not accessible for the member.
     */
    public function find(int $pageId, FrontendUser $user): ?PageModel
    {
        if ($pageId < 1) {
            return null;
        }

        $page = PageModel::findPublishedById($pageId);
        if (!$page instanceof PageModel) {
            return null;
        }

        $granted = PageAccess::isGranted(
            (bool) $page->protected,
            StringUtil::deserialize($page->groups, true),
            \is_array($user->groups) ? $user->groups : []
        );

        return $granted ? $page : null;
    }
}
