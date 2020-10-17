<?php

namespace Services\PeopleService\Regens\StaffRegen;



use Services\PeopleService\PersonConfig\Staff\StaffAttributes;
use Services\PeopleService\PersonConfig\Staff\StaffRoleAttributes;

/**
 * Class PlayerInitialAttributes
 *
 * @package Services\PlayerService\PlayerCreation
 */
class InitialAttributes
{
    public function __construct()
    {
        $this->allAttributes = [];
    }

    public function getAllAttributeValues(array $categoriesPotential)
    {
        $this->setPotentialByCategory($categoriesPotential);

        return $this->allAttributes;
    }

    /*
     * Need a staff type to decide on attributes
     *
     * Provide a type of coach to decide on goalkeeping attributes
     * Managers and will have better tactical knowledge
     * Judging player potential and ability will be better for managers and scouts
     */

    /*
     * Instead of having position, it should use a tactics type for managers or job type for coaches and scouts
     * to determine the attributes
     * Attack minded manager will have better attacking coaching
     * Defence coach will have better defensive attributes
     */

    protected function setPotentialByCategory(array $categoriesPotential)
    {
        $staffRoleAttributes = new StaffRoleAttributes();
        $attributesByCategory = $staffRoleAttributes->getAllAttributesByCategory();

        foreach ($attributesByCategory as $category => $fields) {
            foreach ($fields as $field) {
                $this->allAttributes[$field] = (int) round(
                    rand(
                        $categoriesPotential[$category] - 15,
                        $categoriesPotential[$category]
                    ) / 10
                );
            }
        }
    }
}
