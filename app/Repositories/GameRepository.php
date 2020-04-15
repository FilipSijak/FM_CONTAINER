<?php

namespace App\Repositories;

use App\Http\Resources\Club\ClubResource;
use App\Http\Resources\Game\CompetitionResource;
use App\Http\Resources\Game\CountryResource;
use App\Models\Game\BaseClubs;
use App\Models\Game\BaseCompetitions;
use App\Models\Game\BaseCountries;
use App\Repositories\Interfaces\GameRepositoryInterface;

class GameRepository implements GameRepositoryInterface
{
    /**
     * @var BaseClubs
     */
    protected $baseClubs;

    /**
     * @var BaseCountries
     */
    protected $baseCountries;

    /**
     * @var BaseCompetitions
     */
    protected  $baseCompetitions;

    /**
     * GameRepository constructor.
     *
     * @param BaseClubs        $baseClubs
     * @param BaseCountries    $baseCountries
     * @param BaseCompetitions $baseCompetitions
     */
    public function __construct(
        BaseClubs $baseClubs,
        BaseCountries $baseCountries,
        BaseCompetitions $baseCompetitions
    ) {
        $this->baseClubs        = $baseClubs;
        $this->baseCountries    = $baseCountries;
        $this->baseCompetitions = $baseCompetitions;
    }

    /**
     * Provides options for selecting country, competition and club for a new game from base data
     *
     * @return array
     */
    public function getBaseData(): array
    {
        $baseClubs        = $this->baseClubs::all();
        $baseCountries    = $this->baseCountries::all();
        $baseCompetitions = $this->baseCompetitions::all();

        return [
            'clubs'        => ClubResource::collection($baseClubs),
            'competitions' => CompetitionResource::collection($baseCompetitions),
            'countries'    => CountryResource::collection($baseCountries),
        ];
    }
}