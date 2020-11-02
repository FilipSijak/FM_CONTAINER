<?php

namespace Services\PeopleService\PersonConfig\Player;

/*
 * Highest valued attributes for each position
*/

class PlayerPositionConfig
{
    const POSITION_TECH_ATTRIBUTES = [
        'CB'  => [
            'primary'   => ['heading', 'tackling'],
            'secondary' => [],
        ],
        'LB'  => [
            'primary'   => ['crossing'],
            'secondary' => ['tackling', 'passing'],
        ],
        'LWB' => [
            'primary'   => ['crossing'],
            'secondary' => ['passing', 'first_touch', 'dribbling'],
        ],
        'RB'  => [
            'primary'   => ['crossing'],
            'secondary' => ['tackling', 'passing'],
        ],
        'RWB' => [
            'primary'   => ['crossing'],
            'secondary' => ['passing', 'first_touch', 'dribbling'],
        ],
        'DMC' => [
            'primary'   => ['tackling'],
            'secondary' => ['passing'],
        ],
        'CM'  => [
            'primary'   => ['passing'],
            'secondary' => ['first_touch'],
        ],
        'AMC' => [
            'primary'   => ['passing', 'first_touch'],
            'secondary' => ['finishing', 'dribbling'],
        ],
        'LW'  => [
            'primary'   => ['crossing', 'dribbling', 'first_touch'],
            'secondary' => ['passing'],
        ],
        'LF'  => [
            'primary'   => ['finishing', 'dribbling', 'first_touch'],
            'secondary' => ['passing'],
        ],
        'RW'  => [
            'primary'   => ['crossing', 'dribbling', 'first_touch'],
            'secondary' => ['passing'],
        ],
        'RF'  => [
            'primary'   => ['finishing', 'dribbling', 'first_touch'],
            'secondary' => ['passing'],
        ],
        'CF'  => [
            'primary'   => ['finishing', 'dribbling', 'first_touch'],
            'secondary' => [],
        ],
        'ST'  => [
            'primary'   => ['finishing', 'dribbling', 'first_touch'],
            'secondary' => ['heading'],
        ],
    ];

    const POSITION_MENTAL_ATTRIBUTES = [
        'CB'  => [
            'primary'   => ['positioning', 'determination'],
            'secondary' => ['concentration'],
        ],
        'LB'  => [
            'primary'   => ['positioning', 'workrate'],
            'secondary' => ['long_throws'],
        ],
        'LWB' => [
            'primary'   => ['positioning', 'workrate'],
            'secondary' => ['of_the_ball'],
        ],
        'RB'  => [
            'primary'   => ['positioning', 'workrate'],
            'secondary' => ['of_the_ball'],
        ],
        'RWB' => [
            'primary'   => ['positioning', 'workrate'],
            'secondary' => ['of_the_ball'],
        ],
        'DMC' => [
            'primary'   => ['positioning', 'workrate', 'determination'],
            'secondary' => ['teamwork'],
        ],
        'CM'  => [
            'primary'   => ['positioning', 'of_the_ball'],
            'secondary' => ['teamwork', 'creativity'],
        ],
        'AMC' => [
            'primary'   => ['creativity', 'flair'],
            'secondary' => ['of_the_ball'],
        ],
        'LW'  => [
            'primary'   => ['of_the_ball'],
            'secondary' => [],
        ],
        'LF'  => [
            'primary'   => ['of_the_ball', 'flair'],
            'secondary' => ['composure'],
        ],
        'RW'  => [
            'primary'   => ['of_the_ball'],
            'secondary' => [],
        ],
        'RF'  => [
            'primary'   => ['of_the_ball', 'flair'],
            'secondary' => ['composure'],
        ],
        'CF'  => [
            'primary'   => ['of_the_ball', 'flair'],
            'secondary' => ['composure', 'anticipation'],
        ],
        'ST'  => [
            'primary'   => ['composure', 'anticipation'],
            'secondary' => ['flair', 'concentration'],
        ],
    ];

    const POSITION_PHYSICAL_ATTRIBUTES = [
        'CB'  => [
            'primary'   => ['strength'],
            'secondary' => ['jumping'],
        ],
        'LB'  => [
            'primary'   => ['strength', 'acceleration'],
            'secondary' => ['stamina'],
        ],
        'LWB' => [
            'primary'   => ['pace', 'acceleration'],
            'secondary' => ['stamina'],
        ],
        'RB'  => [
            'primary'   => ['strength', 'acceleration'],
            'secondary' => ['stamina'],
        ],
        'RWB' => [
            'primary'   => ['pace', 'acceleration'],
            'secondary' => ['stamina'],
        ],
        'DMC' => [
            'primary'   => ['stamina', 'strength'],
            'secondary' => ['natural_fitness'],
        ],
        'CM'  => [
            'primary'   => ['stamina', 'agility'],
            'secondary' => ['natural_fitness'],
        ],
        'AMC' => [
            'primary'   => ['pace', 'agility'],
            'secondary' => [],
        ],
        'LW'  => [
            'primary'   => ['pace', 'acceleration'],
            'secondary' => ['agility'],
        ],
        'LF'  => [
            'primary'   => ['pace', 'acceleration'],
            'secondary' => ['agility', 'balance'],
        ],
        'RW'  => [
            'primary'   => ['pace', 'acceleration'],
            'secondary' => ['agility'],
        ],
        'RF'  => [
            'primary'   => ['pace', 'acceleration'],
            'secondary' => ['agility', 'balance'],
        ],
        'CF'  => [
            'primary'   => ['pace', 'agility', 'acceleration'],
            'secondary' => [],
        ],
        'ST'  => [
            'primary'   => ['balance', 'agility'],
            'secondary' => ['jumping'],
        ],
    ];

    const PLAYER_POSITIONS = ['CB', 'LB', 'LWB', 'RB', 'RWB', 'DMC', 'CM', 'AMC', 'LW', 'LF', 'RW', 'RF', 'CF', 'ST'];

    /*const PLAYER_POSITIONS = [
        'CB'  => 'Center back',
        'LB'  => 'Left back',
        'LWB' => 'Left wing back',
        'RB'  => 'Right back',
        'RWB' => 'Right wing back',
        'DMC' => 'Defensive midfield center',
        'CM'  => 'Center midfielder',
        'LW'  => 'Left wing',
        'LF'  => 'Left forward',
        'AMC' => 'Attacking midfield center',
        'RW'  => 'Right wing',
        'RF'  => 'Right forward',
        'CF'  => 'Center forward',
        'ST'  => 'Striker',
    ];*/

    public static function getRandomPosition()
    {
        return self::PLAYER_POSITIONS[rand(0, count(self::PLAYER_POSITIONS) - 1)];
    }

    // return array of primary and secondary attributes
    public static function getPositionMainAttributes($position)
    {
        return [
            'technical' => self::POSITION_TECH_ATTRIBUTES[$position],
            'mental'    => self::POSITION_MENTAL_ATTRIBUTES[$position],
            'physical'  => self::POSITION_PHYSICAL_ATTRIBUTES[$position],
        ];
    }
}
