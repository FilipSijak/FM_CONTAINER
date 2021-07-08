<?php

namespace Services\ClubService\GeneratePeople;

use Services\PeopleService\PersonTypes;

/**
 * Class InitialPlayerCreation
 *
 * @package Services\ClubService\GeneratePeople
 */
class InitialClubPeoplePotential
{
    protected const PLAYER_COUNT = 36;

    protected const AVERAGE_PLAYERS_COUNT = 15;

    protected const BELLOW_AVERAGE_COUNT = 20;

    protected const SPECIAL_PLAYERS_COUNT = 5;

    const PLAYER_POSITIONS = ['CB', 'LB', 'LWB', 'RB', 'RWB', 'DMC', 'CM', 'AMC', 'LW', 'LF', 'RW', 'RF', 'CF', 'ST'];

    /**
     * Random player potential coming from club's rank
     * 3 sets of players: average, below and special player potentials based on club rank
     *
     * @param int $rank
     *
     * @return array
     */
    public function getPlayerPotentialAndInitialPosition(int $rank): array
    {
        $playerPotentialList = [];
        $rank                = $rank * 10;
        $positionsCount      = [
            'CB'  => 8,
            'LB'  => 3,
            'RB'  => 3,
            'DMC' => 3,
            'CM'  => 5,
            'AMC' => 3,
            'LF'  => 2,
            'RF'  => 2,
            'LW'  => 2,
            'RW'  => 2,
            'ST'  => 3,
        ];

        for ($i = 1; $i <= self::PLAYER_COUNT; $i++) {
            $newPlayer = new \stdClass();

            if ($i <= 5) {
                // special players
                $newPlayer->potential = rand($rank, 200);
            } elseif ($i > 5 && $i <= 15) {
                // normal players by club rank
                $newPlayer->potential = rand($rank - 15, $rank + 5);
            } else {
                // bellow average players
                $newPlayer->potential = rand($rank - 40, $rank - 20);
            }

            $playerPotentialList[] = $newPlayer;
        }

        shuffle($playerPotentialList);

        foreach ($playerPotentialList as $player) {
            foreach ($positionsCount as $position => $count) {
                if ($count == 0) {
                    continue;
                }

                $player->position = $position;
                $positionsCount[$position]--;
                break;
            }
        }

        return $playerPotentialList;
    }

    public function getStaffPotentialAndRole(int $rank): array
    {
        $staffList  = [];
        $staffRoles = [
            PersonTypes::COACH             => 7,
            PersonTypes::YOUTH_COACH       => 5,
            PersonTypes::PHYSIO            => 3,
            PersonTypes::MANAGER           => 1,
            PersonTypes::ASSISTANT_MANAGER => 1,
        ];

        foreach ($staffRoles as $role => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $newStaffMember = new \stdClass();

                $newStaffMember->role = $role;

                if ($role == PersonTypes::MANAGER) {
                    $newStaffMember->potential = rand($rank, 200);
                } elseif ($role == PersonTypes::ASSISTANT_MANAGER) {
                    $newStaffMember->potential = rand($rank - 20, $rank);
                } elseif ($role == PersonTypes::YOUTH_COACH) {
                    $newStaffMember->potential = rand($rank - 35, $rank - 20);
                } else {
                    $newStaffMember->potential = rand($rank - 10, $rank);
                }

                $staffList[] = $newStaffMember;
            }
        }

        return $staffList;
    }
}
