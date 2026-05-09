# Switon Validation Package

Validation rules, constraints, and error collection for Switon Framework.

## Installation

```bash
composer require switon/validation
```

**Requirements:** PHP 8.3+

## Quick Start

```php
use Switon\Core\Attribute\Autowired;
use Switon\Validating\Attribute\ArrayOf;
use Switon\Validating\Attribute\Email;
use Switon\Validating\Attribute\Length;
use Switon\Validating\Attribute\Required;
use Switon\Validating\ValidatorInterface;

class UserService
{
    #[Autowired] protected ValidatorInterface $validator;

    public function register(array $input): array
    {
        return $this->validator->validateValues($input, [
            'tags' => [new ArrayOf('string', minItems: 1)],
            'username' => [new Required(), new Length(4, 16)],
            'email' => [new Required(), new Email()],
        ]);
    }
}
```

Docs: https://docs.switon.dev/latest/validation

## License

MIT.
