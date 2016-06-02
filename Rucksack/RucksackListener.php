<?php

namespace Statamic\Addons\Rucksack;

use Statamic\API\Request;
use Statamic\Extend\Listener;

class RucksackListener extends Listener
{
    private $rucksack;

    public $events = [
        'Rucksack.add' => 'add',
        'Rucksack.remove' => 'remove',
    ];

    protected function init()
    {
        $this->rucksack = new Rucksack;
    }

    public function add($id)
    {
        $this->rucksack->add($id, Request::all());
    }

    public function remove($id)
    {
        $this->rucksack->remove($id);
    }
}
