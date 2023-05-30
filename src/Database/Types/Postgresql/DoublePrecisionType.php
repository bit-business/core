<?php

namespace NadzorServera\Skijasi\Database\Types\Postgresql;

use NadzorServera\Skijasi\Database\Types\Common\DoubleType;

class DoublePrecisionType extends DoubleType
{
    const NAME = 'double precision';
    const DBTYPE = 'float8';
}
