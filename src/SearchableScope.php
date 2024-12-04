<?php

namespace Laravel\Scout;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ScopeInterface as ScopeOld;
use Illuminate\Database\Eloquent\Scope as Scope;
use Laravel\Scout\Events\ModelsFlushed;
use Laravel\Scout\Events\ModelsImported;

if (! interface_exists(Scope::class)) {
    class_alias(ScopeOld::class, Scope::class);
}

class SearchableScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(EloquentBuilder $builder, Model $model)
    {
        $this->extend($builder);
    }

    /**
     * Remove the scope from the given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     *
     * @return void
     */
    public function remove(EloquentBuilder $builder, Model $model)
    {
        //
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(EloquentBuilder $builder)
    {
        $builder->macro('searchable', function (EloquentBuilder $builder, $chunk = null) {
            $builder->chunk($chunk ?: config('scout.chunk.searchable', 500), function ($models) {
                $models->filter(fn ($model) => $model->shouldBeSearchable())->searchable();

                event(new ModelsImported($models));
            });
        });

        $builder->macro('unsearchable', function (EloquentBuilder $builder, $chunk = null) {
            $builder->chunk($chunk ?: config('scout.chunk.unsearchable', 500), function ($models) {
                $models->unsearchable();

                event(new ModelsFlushed($models));
            });
        });

        // TODO: add macros to HasManyThrough
    }
}
