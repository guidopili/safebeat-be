<?php declare(strict_types=1);

namespace Safebeat\DBAL;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class LanguageEnum extends Type
{
    private const ALLOWED_LANGUAGES = ['en', 'it', 'fr'];

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return sprintf("ENUM('%s')", implode("','", self::ALLOWED_LANGUAGES));
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return $value;
        }

        if (false === self::validateValue($value)) {
            throw new \InvalidArgumentException("Invalid status");
        }

        return $value;
    }

    public function getName(): string
    {
        return 'language_enum';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public static function validateValue(string $value): bool
    {
        return in_array($value, self::ALLOWED_LANGUAGES, true);
    }
}
