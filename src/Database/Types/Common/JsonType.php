<?php

namespace NadzorServera\Skijasi\Database\Types\Common;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use NadzorServera\Skijasi\Database\Types\Type;

class JsonType extends Type
{
    const NAME = 'json';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'json';
    }
}
