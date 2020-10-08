<?php

namespace Services\PeopleService\PlayerConfig;

/*
 * Player mental attributes
*/

class PlayerMentalAttributes
{
    const PRIMARY_MENTAL_ATTRIBUTES = [
        'forward'               => ['anticipation', 'flair', 'composure'],
        'defending_middfielder' => ['workRate', 'determination'],
        'creative_middfielder'  => ['creativity'],
        'center_back'           => ['positioning', 'decisions'],
        'wing_back'             => ['of_the_ball', 'workRate'],
        'winger'                => ['of_the_ball'],
    ];

    const SECONDARY_MENTAL_ATTRIBUTES = [
        'forward'               => ['of_the_ball'],
        'defending_middfielder' => ['teamWork', 'positioning'],
        'creative_middfielder'  => ['flair', 'of_the_ball'],
        'center_back'           => ['composure', 'concentration', 'anticipation'],
        'wing_back'             => ['positioning'],
        'winger'                => ['flair', 'workRate'],
    ];
}
