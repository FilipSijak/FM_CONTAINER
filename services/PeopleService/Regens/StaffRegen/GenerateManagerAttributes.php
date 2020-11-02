<?php

namespace Services\PeopleService\Regens\StaffRegen;

use Services\PeopleService\PersonConfig\Staff\StaffAttributes;
use Services\PeopleService\PersonPotential\PersonPotential;
use Services\PeopleService\Regens\PersonAttributesGenerator;

class GenerateManagerAttributes extends PersonAttributesGenerator
{
    /**
     * @var PersonPotential
     */
    private $personPotential;

    public function generateAttributes()
    {
        $this->personPotential = new PersonPotential();

        $this->setCategoriesPotential();

        $this->setInitialAttributes();

        $this->setPersonInfo();

        return $this->person;
    }

    protected function setCategoriesPotential()
    {
        $this->personPotential->setPersonCategories(StaffAttributes::STAFF_CATEGORIES);
        $this->person->potentialByCategory = (array)$this->personPotential->calculatePersonPotential($this->rank);
    }

    protected function setInitialAttributes()
    {
        $initialAttributes = new InitialAttributes();
        $allGeneratedAttributes = $initialAttributes->getAllAttributeValues($this->person->potentialByCategory);

        foreach($allGeneratedAttributes as $attribute => $value) {
            $this->person->{$attribute} = $value;
        }
    }
}
