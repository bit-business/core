<?php

namespace NadzorServera\Skijasi\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use NadzorServera\Skijasi\Database\Types\Type;

class MacAddrType extends Type
{
    const NAME = 'macaddr';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'macaddr';
    }
}
