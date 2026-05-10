<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Fixtures;

use Switon\Core\LocaleInterface;
use Switon\Core\TranslatorInterface;

/**
 * Test-only translator backed by in-memory catalogs (no switon/i18n dependency).
 *
 * Locale lookup order matches common split-tag fallback (e.g. zh-hk → zh → en).
 *
 * @see \Switon\Core\TranslatorInterface
 */
final class CatalogTranslator implements TranslatorInterface
{
    /**
     * @param array<string, array<string, string>> $messagesByLocale
     */
    public function __construct(
        private LocaleInterface $locale,
        private array $messagesByLocale,
    ) {
    }

    public function translate(string $id, array $bind = [], bool $useICU = false): string
    {
        $text = $this->lookup($id);
        if ($bind === []) {
            return $text;
        }

        foreach ($bind as $key => $value) {
            $text = str_replace('{' . $key . '}', (string)$value, $text);
        }

        return $text;
    }

    public function has(string $id): bool
    {
        return $this->lookupRaw($id) !== null;
    }

    private function lookup(string $id): string
    {
        return $this->lookupRaw($id) ?? $id;
    }

    private function lookupRaw(string $id): ?string
    {
        foreach ($this->localeCandidates() as $localeKey) {
            if (isset($this->messagesByLocale[$localeKey][$id])) {
                return $this->messagesByLocale[$localeKey][$id];
            }
        }

        return null;
    }

    /** @return list<string> */
    private function localeCandidates(): array
    {
        $raw = strtolower(str_replace('_', '-', $this->locale->get()));
        $candidates = [$raw];
        if (str_contains($raw, '-')) {
            $candidates[] = explode('-', $raw, 2)[0];
        }
        $candidates[] = 'en';

        return array_values(array_unique($candidates));
    }
}
