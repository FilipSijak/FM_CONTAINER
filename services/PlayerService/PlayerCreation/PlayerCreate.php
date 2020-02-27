<?php

namespace Services\PlayerService\PlayerCreation;

use App\Models\Player\Player;
use App\Models\Player\Position;
use Illuminate\Support\Facades\Date;
use Services\PlayerService\PlayerPosition\PlayerPosition;
use Services\PlayerService\PlayerPotential\PlayerPotential;
use Services\PlayerService\PlayerCreation\PlayerInitialAttributes;

/*use App\Repositories\PlayerRepository;
use App\Player;
use App\Position;*/

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
        $playerPositionList             = $this->setPlayerPositionList($initialAttributeValues);
        $player['player_position_list'] = $playerPositionList;
        // set player info
        /*$playerInfo = $this->setPlayerInfo();
        $player['player_info'] = $playerInfo;*/


        $player['player_info'] = [
            'first_name'   => 'Filip',
            'last_name'    => 'Sijak',
            'country_code' => 'GBR',
            'dob'          => date("Ymd"),
        ];

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
        $player->game_id = 1;
        //$player->save();

        $positions = Position::all();
        dd($playerCreationData['player_position_list']);
        foreach ($positions as $position) {
            $grade = $playerCreationData['player_position_list'][$position->name];
            $grade = ceil($grade);
            dd($grade);
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
        $pr = new PlayerRepository();
        return $pr->setPlayerInitialInfo();
    }
}
