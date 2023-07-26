<?php

namespace NadzorServera\Skijasi\Events;

use Illuminate\Queue\SerializesModels;
use NadzorServera\Skijasi\Models\DataType;

class CRUDDataDeleted
{
    use SerializesModels;

    public $data_type;

    public $data;

    public function __construct(DataType $data_type)
    {
        $this->data_type = $data_type;

        event(new CRUDDataChanged($data_type, null, 'Deleted'));
    }
}
