<?php

namespace Services\PeopleService\PersonConfig\Player;

/*
 * Player technical attributes
*/

class PlayerTechAttributes
{
    const PRIMARY_TECH_ATTRIBUTES = [
        'forward'               => ['finishing', 'dribbling', 'firstTouch'],
        'defending_middfielder' => ['tackling', 'marking'],
        'creative_middfielder'  => ['passing', 'firstTouch'],
        'center_back'           => ['tackling', 'heading', 'marking'],
        'wing_back'             => ['crossing', 'tackling'],
        'winger'                => ['dribbling', 'crossing', 'passing'],
    ];

    const SECONDARY_TECH_ATTRIBUTES = [
        'forward'               => ['technique', 'penalty_taking'],
        'defending_middfielder' => ['passing', 'heading'],
        'creative_middfielder'  => ['technique', 'dribbling'],
        'center_back'           => [],
        'wing_back'             => ['marking', 'long_throws'],
        'winger'                => ['firstTouch'],
    ];
}
