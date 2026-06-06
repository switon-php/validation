# Switon Validation Package

[![CI](https://img.shields.io/github/actions/workflow/status/switon-php/validation/ci.yml?branch=main&label=CI)](https://github.com/switon-php/validation/actions/workflows/ci.yml) [![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-777BB4)](https://www.php.net/)

Switon's validation layer for constraint attributes, typed input conversion, and localized error messages.

## Highlights

- **Constraint attributes:** `Required`, `Length`, `Email`, and related attributes validate fields directly.
- **Typed values:** `Type` and `Defaults` can fill and cast values during validation.
- **Validation flow:** one-shot and manual validation are both supported.
- **Localized messages:** template files provide per-locale fallback messages and labels.

## Installation

```bash
composer require switon/validation
```

## Quick Start

```php
use Switon\Validating\Attribute\Defaults;
use Switon\Validating\Attribute\Email;
use Switon\Validating\Attribute\Length;
use Switon\Validating\Attribute\Required;
use Switon\Validating\Attribute\Type;
use Switon\Core\Attribute\Autowired;
use Switon\Validating\ValidatorInterface;

final class UserService
{
    #[Autowired] protected ValidatorInterface $validator;

    public function register(array $input): array
    {
        return $this->validator->validateValues($input, [
            'email' => [new Required(), new Email()],
            'name' => [new Required(), new Length(2, 32)],
            'age' => [new Defaults(18), new Type('int')],
        ]);
    }
}
```

Docs: https://docs.switon.dev/latest/validation

## License

MIT.
