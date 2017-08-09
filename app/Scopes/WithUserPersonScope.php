<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class WithUserPersonScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder
            ->select('users.*')
            ->join('people', 'users.id', '=', 'people.user_id')
            ->addSelect([
                'people.first_name as first_name',
                'people.last_name as last_name',
                'people.email as email',
                'people.phone as phone',
            ]);
    }
}