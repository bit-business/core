<?php

namespace NadzorServera\Skijasi\Database\Types\Common;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use NadzorServera\Skijasi\Database\Types\Type;

class TextType extends Type
{
    const NAME = 'text';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'text';
    }
}
