<?php

namespace App\Traits;

use DB;
use App\Sniffers\ModelSniffer;
use Exception;

trait CanSave
{
    public function saveWithRelations(array $attributes = [], array $relations = [])
    {
        if ($this->wasRecentlyCreated === true) {
            //$this->createWithRelations();
        } else {
            $this->updateWithRelations($attributes, $relations);
        }
    }

    public function updateWithRelations(array $attributes = [], array $relations = [])
    {
        $rel = (new ModelSniffer())->getAllRelations($this);

        if (empty($relations)) {
            $relations = array_keys($rel);
        }

        try {
            DB::transaction(function () use ($attributes, $relations, $rel) {
                $this->update($attributes);


                foreach ($relations as $relation) {
                    if (!isset($rel[$relation])) {
                        continue;
                    }

                    switch ($rel[$relation]['type']) {
                        case 'Illuminate\Database\Eloquent\Relations\BelongsTo':
                            $this->{$relation}()->update($attributes[$relation]);
                            break;
                        case 'Illuminate\Database\Eloquent\Relations\HasOne':
                            $this->{$relation}()->update($attributes[$relation]);
                            break;
                        case 'Illuminate\Database\Eloquent\Relations\BelongsToMany':
                            $this->{$relation}()->sync($attributes[$relation]);
                            break;
                    }
                }
            });

            dd('aici');
        } catch (Exception $e) {
            dd($e);
        }
    }
}
