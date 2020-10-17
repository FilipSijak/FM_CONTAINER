<?php

namespace Services\PeopleService\PersonConfig\Staff;

class StaffRoleAttributes
{
    public function getAllAttributesByCategory()
    {
        return [
            'coaching' => StaffAttributes::COACHING,
            'goalkeeping' => StaffAttributes::GOALKEEPING,
            'knowledge' => StaffAttributes::KNOWLEDGE,
            'mental' => StaffAttributes::MENTAL
        ];
    }

    public function getAllAttributes()
    {
        return array_merge(
            StaffAttributes::COACHING,
            StaffAttributes::GOALKEEPING,
            StaffAttributes::KNOWLEDGE,
            StaffAttributes::MENTAL
        );
    }
}
