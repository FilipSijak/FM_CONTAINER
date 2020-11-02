<?php

namespace Services\PeopleService\PersonConfig\Staff;

/*
 * All the attributes manager/coaches can have
*/

class StaffAttributes
{
    public const COACHING = [
        'attacking', 'defending', 'fitness', 'mental', 'tactical', 'technical', 'working_with_youngsters'
    ];

    public const MENTAL = [
        'adaptability', 'determination', 'discipline', 'man_management', 'motivating'
    ];

    public const KNOWLEDGE = [
        'judging_player_potential', 'judging_player_ability', 'judging_staff_ability', 'negotiating', 'tactics'
    ];

    public const GOALKEEPING = [
        'distribution', 'handling', 'shot_stopping'
    ];

    public const STAFF_CATEGORIES = ['coaching', 'mental', 'goalkeeping', 'knowledge'];
}
