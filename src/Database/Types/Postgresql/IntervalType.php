<?php

namespace NadzorServera\Skijasi\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use NadzorServera\Skijasi\Database\Types\Type;

class IntervalType extends Type
{
    const NAME = 'interval';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'interval';
    }
}
