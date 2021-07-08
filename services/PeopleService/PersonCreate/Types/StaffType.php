<?php

namespace Services\PeopleService\PersonCreate\Types;

use App\Models\People\Staff as StaffModel;
use stdClass;

class StaffType
{
    public function create(stdClass $generatedPersonAttributes, int $gameId, string $staffType)
    {
        $person          = new StaffModel();
        $person->game_id = $gameId;
        $person->type    = $staffType;

        foreach ($generatedPersonAttributes as $field => $value) {
            if ($field == 'potentialByCategory') {
                continue;
            }

            $person->{$field} = $value;
        }

        $person->save();

        return $person;
    }
}
