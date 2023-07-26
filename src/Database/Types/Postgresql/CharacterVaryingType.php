<?php

namespace NadzorServera\Skijasi\Database\Types\Postgresql;

use NadzorServera\Skijasi\Database\Types\Common\VarCharType;

class CharacterVaryingType extends VarCharType
{
    const NAME = 'character varying';
    const DBTYPE = 'varchar';
}
