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

namespace Espin\MemberJumpToBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Espin\MemberJumpToBundle\Model\JumpToList;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Keeps the list of target pages of a module consistent.
 *
 * @final
 */
class ModuleSettingsListener
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Stores the list with exactly one default and refuses a page which is listed twice.
     */
    #[AsCallback(table: 'tl_module', target: 'fields.memberJumpToPages.save')]
    public function onSave(mixed $value): string
    {
        $rows = \is_string($value) && '' !== $value ? @\unserialize($value, ['allowed_classes' => false]) : [];
        $rows = \is_array($rows) ? $rows : [];

        $list = JumpToList::fromStored($rows);
        if (\count($list->entries()) !== \count($rows)) {
            throw new \RuntimeException(
                $this->translator->trans('ERR.memberJumpToInvalidList', [], 'contao_default')
            );
        }

        return \serialize(\array_map(
            static fn ($entry): array => [
                'page'    => $entry->pageId,
                'label'   => $entry->label,
                'default' => $entry->default ? '1' : '',
            ],
            $list->entries()
        ));
    }
}
