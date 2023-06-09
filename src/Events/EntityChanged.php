<?php

namespace NadzorServera\Skijasi\Events;

use Illuminate\Queue\SerializesModels;
use NadzorServera\Skijasi\Models\DataType;

class EntityChanged
{
    use SerializesModels;

    public $data_type;

    public $data;

    public $change_type;

    public function __construct(DataType $data_type, $data, $change_type)
    {
        $this->data_type = $data_type;

        $this->data = $data;

        $this->change_type = $change_type;
    }
}
