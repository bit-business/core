<?php

namespace NadzorServera\Skijasi\Database\Types\Mysql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use NadzorServera\Skijasi\Database\Types\Type;

class MediumIntType extends Type
{
    const NAME = 'mediumint';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        $common_integer_type_declaration = call_protected_method($platform, '_getCommonIntegerTypeDeclarationSQL', $field);

        return 'mediumint'.$common_integer_type_declaration;
    }
}
