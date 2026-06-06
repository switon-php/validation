<?php

declare(strict_types=1);

namespace Switon\Validating;

/**
 * Constraint contract for field display-label resolution in error messages.
 *
 * Use when a constraint wants validation messages to refer to friendly labels
 * instead of raw field paths.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation::validate()
 */
interface LabelAwareConstraintInterface
{
    /**
     * Return display-label overrides keyed by field name or field path.
     *
     * @param string|null $field Current field name/path when available
     *
     * @return array<string, string>
     */
    public function getLabels(?string $field = null): array;
}
