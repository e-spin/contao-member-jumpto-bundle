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
 * The list of target pages which is configured at a module.
 */
final class JumpToList
{
    /** @param list<JumpToEntry> $entries */
    private function __construct(private readonly array $entries)
    {
    }

    /**
     * Reads the value of the wizard field, broken rows are skipped so a damaged setting never breaks the page.
     */
    public static function fromStored(mixed $stored): self
    {
        $rows = self::rows($stored);

        // The same page may be listed several times, e.g. with another label.
        $entries = [];
        foreach ($rows as $row) {
            $pageId = \is_array($row) ? (int) ($row['page'] ?? 0) : 0;
            if ($pageId < 1) {
                continue;
            }

            $entries[] = new JumpToEntry($pageId, \trim((string) ($row['label'] ?? '')), !empty($row['default']));
        }

        return new self(self::ensureOneDefault($entries));
    }

    /** @return list<JumpToEntry> */
    public function entries(): array
    {
        return $this->entries;
    }

    /**
     * The value for the wizard field, the counterpart of fromStored().
     */
    public function toStored(): string
    {
        return \serialize(\array_map(
            static fn (JumpToEntry $entry): array => [
                'page'    => $entry->pageId,
                'label'   => $entry->label,
                'default' => $entry->default ? '1' : '',
            ],
            $this->entries
        ));
    }

    /**
     * The rows of a stored value as they are, also the broken ones.
     *
     * @return list<mixed>
     */
    public static function rows(mixed $stored): array
    {
        if (\is_string($stored) && '' !== $stored) {
            $stored = @\unserialize($stored, ['allowed_classes' => false]);
        }

        return \is_array($stored) ? \array_values($stored) : [];
    }

    /**
     * @param list<JumpToEntry> $entries
     *
     * @return list<JumpToEntry>
     */
    private static function ensureOneDefault(array $entries): array
    {
        $defaults = \array_filter($entries, static fn (JumpToEntry $entry): bool => $entry->default);
        $first    = [] === $defaults ? 0 : \array_key_first($defaults);

        // Without a mark the first page is the default, with several marks the first mark wins.
        return \array_map(
            static fn (JumpToEntry $entry, int $index): JumpToEntry => new JumpToEntry(
                $entry->pageId,
                $entry->label,
                $index === $first
            ),
            $entries,
            \array_keys($entries)
        );
    }
}
