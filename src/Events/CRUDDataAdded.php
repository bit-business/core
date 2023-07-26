<?php

namespace NadzorServera\Skijasi\Events;

use Illuminate\Queue\SerializesModels;
use NadzorServera\Skijasi\Models\DataType;

class CRUDDataAdded
{
    use SerializesModels;

    public $data_type;

    public $data;

    public function __construct(DataType $data_type, $data)
    {
        $this->data_type = $data_type;

        $this->data = $data;

        event(new CRUDDataChanged($data_type, $data, 'Added'));
    }
}
