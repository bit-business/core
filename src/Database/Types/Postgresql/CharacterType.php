<?php

namespace NadzorServera\Skijasi\Database\Types\Postgresql;

use NadzorServera\Skijasi\Database\Types\Common\CharType;

class CharacterType extends CharType
{
    const NAME = 'character';
    const DBTYPE = 'bpchar';
}
