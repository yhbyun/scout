<?php

namespace Laravel\Scout;

use Illuminate\Support\Str;

trait ModelBugFixer
{
    /**
     * Qualify the given column name by the model's table.
     *
     * @param  string  $column
     * @return string
     */
    public function qualifyColumn($column)
    {
        if (Str::contains($column, '.')) {
            return $column;
        }

        return $this->getTable().'.'.$column;
    }
}
