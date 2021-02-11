<?php

namespace App\Models\Player;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    public $timestamps = false;

    public function positions()
    {
        return $this->belongsToMany('App\Models\Player\Position');
    }

    public function clubs()
    {
        return $this->belongsToMany('App\Models\Club\Club', 'player_club');
    }

    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public function setPositions(array $positions)
    {
        $this->generatedPosition = $positions;
    }

    public function getPositions()
    {
        return $this->generatedPosition;
    }

    public function setAttributesCategoriesPotential(array $categories)
    {
        $this->attributesCategories = $categories;
    }

    public function getAttributeCategoriesPotential()
    {
        return $this->attributesCategories["potentialByCategory"];
    }
}
