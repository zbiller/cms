<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Kalnoy\Nestedset\NodeTrait;
use Kalnoy\Nestedset\QueryBuilder;

trait HasNodes
{
    use NodeTrait;

    /**
     * Get a new base query that includes deleted nodes.
     *
     * @since 1.1
     *
     * @param string|null $table
     * @return QueryBuilder
     */
    public function newNestedSetQuery($table = null)
    {
        $builder = $this->newQuery();

        if (array_key_exists(SoftDeletes::class, class_uses($this))) {
            $builder->withTrashed();
        }

        if (array_key_exists(HasDrafts::class, class_uses($this))) {
            $builder->withDrafts();
        }

        return $this->applyNestedSetScope($builder, $table);
    }
}
