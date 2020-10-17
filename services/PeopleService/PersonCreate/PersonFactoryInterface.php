<?php

namespace Services\PeopleService\PersonCreate;

use App\Models\Player\Player;
use App\Models\People\Staff;

interface PersonFactoryInterface
{
    public function createPlayer(): Player;
    public function createManager(): Staff;
}
