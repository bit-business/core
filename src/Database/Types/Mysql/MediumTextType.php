<?php

namespace NadzorServera\Skijasi\Database\Types\Mysql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use NadzorServera\Skijasi\Database\Types\Type;

class MediumTextType extends Type
{
    const NAME = 'mediumtext';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'mediumtext';
    }
}
