<?php

namespace Services\PeopleService\Regens;

use Faker\Factory;
use Services\PeopleService\PersonTypes;
use stdClass;

abstract class PersonAttributesGenerator
{
    /**
     * @var stdClass
     */
    protected $person;

    /**
     * @var stdClass
     */
    protected $personConfig;
    /**
     * @var int
     */
    private   $personType;

    public function __construct(stdClass $personConfig, string $type)
    {
        $this->personConfig = $personConfig;
        $this->person       = new stdClass();
        $this->personType   = $type;
    }

    abstract public function generateAttributes();

    abstract protected function setCategoriesPotential();

    abstract protected function setInitialAttributes();

    protected function setPersonInfo()
    {
        $faker     = Factory::create();
        $startDate = '';
        $endDate   = '';

        switch ($this->personType) {
            case PersonTypes::PLAYER:
                $startDate = '-40 years';
                $endDate   = '-16 years';
                break;
            case PersonTypes::MANAGER:
                $startDate = '-70 years';
                $endDate   = '-35 years';
                break;
        }

        $dob = $faker->dateTimeBetween($startDate, $endDate, $timezone = null);
        $dob = date_format($dob, 'Y-m-d');

        $this->person->first_name   = $faker->firstNameMale;
        $this->person->last_name    = $faker->lastName;
        $this->person->country_code = 'GBR';
        $this->person->dob          = $dob;
    }
}