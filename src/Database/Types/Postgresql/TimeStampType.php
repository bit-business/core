<?php

namespace NadzorServera\Skijasi\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use NadzorServera\Skijasi\Database\Types\Type;

class TimeStampType extends Type
{
    const NAME = 'timestamp';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'timestamp(0) without time zone';
    }
}
