<?php

namespace NadzorServera\Skijasi\Database\Types\Mysql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use NadzorServera\Skijasi\Database\Types\Type;

class LineStringType extends Type
{
    const NAME = 'linestring';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'linestring';
    }
}
