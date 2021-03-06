<?php

namespace Services\PeopleService;

use Services\PeopleService\Interfaces\PeopleServiceInterface;
use Services\PeopleService\PersonCreate\PersonFactory;
use Services\PeopleService\PersonValuation\PlayerValuation;
use Services\PeopleService\PlayerPosition\PlayerPosition;
use Services\PeopleService\Regens\PlayerRegen\GeneratePlayerAttributes;
use Services\PeopleService\Regens\StaffRegen\GenerateManagerAttributes;
use Services\PeopleService\Repositories\PlayerRepository;

class PeopleService implements PeopleServiceInterface
{
    /**
     * @var int
     */
    private $gameId;

    /**
     * @var int
     */

    private $personType;

    private $person = null;
    private $personConfig;

    /**
     * @param int $personPotential
     * @param int $gameId
     * @param int $personType
     *
     * @return $this
     */
    public function setPersonConfiguration(\stdClass $personConfig, int $gameId, string $personType): PeopleService
    {
        $this->personConfig = $personConfig;
        $this->gameId       = $gameId;
        $this->personType   = $personType;

        return $this;
    }

    /**
     * @param int $clubRank
     * @param int $leagueRank
     *
     * @return \App\Models\People\Staff|\App\Models\Player\Player
     */
    public function createPerson(int $clubRank = null, int $leagueRank = null)
    {
        $personFactory = new PersonFactory($this->gameId);

        switch ($this->personType) {
            case PersonTypes::PLAYER:
                $player = new GeneratePlayerAttributes($this->personConfig, PersonTypes::PLAYER);
                $generatedAttributes = $player->generateAttributes();
                $this->person        = $personFactory->setAttributes($generatedAttributes)->createPlayer();

                $playerValuation = new PlayerValuation();

                $playerValuation->setPersonValue(
                    $this->personConfig->potential,
                    $clubRank,
                    $leagueRank,
                    $this->person->dob
                );

                $this->person->value = $playerValuation->getPlayerValue();

                break;
            case PersonTypes::MANAGER:
                $manager             = new GenerateManagerAttributes($this->personConfig, PersonTypes::MANAGER);
                $generatedAttributes = $manager->generateAttributes();
                $this->person        = $personFactory->setAttributes($generatedAttributes)->createManager();

                break;
        }

        return $this->person;
    }

    /**
     * @param array $playerAttributes
     *
     * @return array
     */
    public function generatePlayerPositionList(array $playerAttributes): array
    {
        $playerPosition = new PlayerPosition();

        return $playerPosition->getInitialPositionsBasedOnAttributes($playerAttributes);
    }

    /**
     * @param int $clubId
     *
     * @return array
     */
    public function playersByClubId(int $clubId)
    {
        $playerRepository = new PlayerRepository();

        return $playerRepository->getPlayersByClubId($clubId);
    }
}
