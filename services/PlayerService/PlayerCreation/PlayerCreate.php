<?php

namespace Services\PlayerService\PlayerCreation;

use App\Models\Player\Player;
use App\Models\Player\Position;
use Services\PlayerService\PlayerPosition\PlayerPosition;
use Services\PlayerService\PlayerPotential\PlayerPotential;
use Services\PlayerService\PlayerCreation\PlayerInitialAttributes;
use Faker\Factory;

/*
 * Creation of a new player (regen)
*/

class PlayerCreate
{
    public function setupPlayer($coeff)
    {
        $player = [];

        // player potential
        $playerPotential            = (array)$this->setPlayerPotential($coeff);
        $player['player_potential'] = $playerPotential;

        // player position
        $playerPosition = $this->setPlayerPosition();

        // player attributes
        $initialAttributeValues      = $this->setPlayerInitialAttributes($playerPotential, $playerPosition);
        $player['player_attributes'] = $initialAttributeValues;

        // player other positions based on attributes
        $playerPositionList = $this->setPlayerPositionList($initialAttributeValues);

        $player['player_position_list'] = $playerPositionList;
        // set player info
        $playerInfo = $this->setPlayerInfo();

        $player['player_info'] = $playerInfo;

        return $this->setPlayerInstance($player);
    }

    private function setPlayerInstance($playerCreationData)
    {
        $player = new Player();

        foreach ($playerCreationData as $item => $fields) {
            if (is_array($fields)) {
                if ($item == 'player_position_list') {
                    continue;
                }

                $this->setPlayerProperties($player, $fields);
                continue;
            }

            $player->{$item} = $fields;
        }

        $player->game_id      = 1;
        $player->first_name   = $playerCreationData['player_info']['first_name'];
        $player->last_name    = $playerCreationData['player_info']['last_name'];
        $player->country_code = $playerCreationData['player_info']['country_code'];
        $player->dob          = $playerCreationData['player_info']['dob'];
        $player->save();

        $positions = Position::all();

        foreach ($positions as $position) {
            $grade = $playerCreationData['player_position_list'][$position->alias];
            $grade = ceil($grade);
            $player->positions()->attach($position, ['position_grade' => $grade, 'game_id' => 1]);
        }

        return $player;
    }

    private function setPlayerProperties(Player $player, array $fields)
    {
        foreach ($fields as $field => $value) {
            $player->{$field} = $value;
        }
    }

    private function setPlayerPotential($coeff)
    {
        return PlayerPotential::calculatePlayerPotential($coeff);
    }

    private function setPlayerPosition()
    {
        return PlayerPosition::setRandomPosition();
    }

    private function setPlayerInitialAttributes($playerPotential, $playerPosition): array
    {
        $playerAttributesInstance = new PlayerInitialAttributes($playerPotential, $playerPosition);
        return $playerAttributesInstance->getAllAttributeValues();

    }

    private function setPlayerPositionList($initialAttributeValues): array
    {
        return PlayerPosition::setInitialPositionsBasedOnAttributes($initialAttributeValues);
    }

    private function setPlayerInfo()
    {
        $faker = Factory::create();
        $dob   = $faker->dateTimeBetween($startDate = '-40 years', $endDate = '-16 years', $timezone = null);
        $dob   = date_format($dob, 'Y-m-d');

        return [
            'first_name'   => $faker->firstNameMale,
            'last_name'    => $faker->lastName,
            'country_code' => 'GBR',
            'dob'          => $dob,
        ];
    }
}
