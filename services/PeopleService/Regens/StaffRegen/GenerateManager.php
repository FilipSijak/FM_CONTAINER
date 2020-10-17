<?php

namespace Services\PeopleService\Regens\StaffRegen;

use Faker\Factory;
use Services\PeopleService\PersonPotential\StaffPotential;

class GenerateManager
{
    public function __construct(int $rank)
    {
        $this->rank           = $rank;
        $this->manager        = new \stdClass();
        $this->staffPotential = new StaffPotential();
    }

    public function makeManager()
    {
        $this->setPotentialBasedOnRank();

        // player attributes
        $this->getInitialAttributes();

        // basic player information
        $this->setPersonInfo();

        return $this->manager;
    }

    private function setPotentialBasedOnRank()
    {
        $this->manager->potentialByCategory = (array)$this->staffPotential->calculatePlayerPotential($this->rank);
    }

    private function getInitialAttributes()
    {
        $initialAttributes = new InitialAttributes();
        $allGeneratedAttributes = $initialAttributes->getAllAttributeValues($this->manager->potentialByCategory);

        foreach($allGeneratedAttributes as $attribute => $value) {
            $this->manager->{$attribute} = $value;
        }
    }

    private function setPersonInfo()
    {
        $faker = Factory::create();
        $dob   = $faker->dateTimeBetween($startDate = '-70 years', $endDate = '-35 years', $timezone = null);
        $dob   = date_format($dob, 'Y-m-d');

        $this->manager->first_name   = $faker->firstNameMale;
        $this->manager->last_name    = $faker->lastName;
        $this->manager->country_code = 'GBR';
        $this->manager->dob          = $dob;
    }
}
