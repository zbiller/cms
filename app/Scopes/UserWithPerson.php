<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserWithPerson implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder  $builder
     * @param Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->join('persons', 'users.id', '=', 'persons.user_id')->addSelect([
            'users.*'
        ])->addSelect([
            'persons.first_name as first_name',
            'persons.last_name as last_name',
            'persons.email as email',
            'persons.phone as phone',
        ]);
    }
}