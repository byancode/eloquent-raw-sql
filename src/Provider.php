<?php

namespace Byancode\EloquentRawSql;;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }
    public function boot()
    {
        \Illuminate\Database\Query\Builder::macro('rawSql', function ($sql, $bindings) {
            return array_reduce($bindings, function ($sql, $binding) {
                return preg_replace('/\?/', json_encode($binding), $sql, 1);
            }, $sql);
        });
        \Illuminate\Database\Query\Builder::macro('toRawSql', function () {
            return $this->rawSql($this->toSql(), $this->getBindings());
        });
        \Illuminate\Database\Eloquent\Builder::macro('toRawSql', function () {
            return $this->getQuery()->toRawSql();
        });
        \Illuminate\Database\Eloquent\Relations\Relation::macro('relationToRawSql', function () {
            return str_replace('id` is null', 'id` = ' . preg_replace('/(\w+)/s', '`$1`', $this->getQualifiedParentKeyName()), $this->toRawSql());
        });
        // -------------------------------
        \Illuminate\Database\Query\Builder::macro('updateToRawSql', function ($values) {
            return $this->rawSql($this->grammar->compileUpdate($this, $values), array_merge(array_values($values), $this->getBindings()));
        });
        \Illuminate\Database\Eloquent\Builder::macro('updateToRawSql', function ($values) {
            return $this->getQuery()->updateToRawSql($values);
        });
        // -------------------------------
        \Illuminate\Database\Query\Builder::macro('deleteToRawSql', function () {
            return $this->rawSql($this->grammar->compileDelete($this), $this->getBindings());
        });
        \Illuminate\Database\Eloquent\Builder::macro('deleteToRawSql', function () {
            return $this->getQuery()->deleteToRawSql();
        });
        // -------------------------------
        \Illuminate\Database\Query\Builder::macro('insertToRawSql', function ($values) {
            if (empty($values)) {
                return true;
            }
            if (!is_array(reset($values))) {
                $values = [$values];
            } else {
                foreach ($values as $key => $value) {
                    ksort($value);
                    $values[$key] = $value;
                }
            }
            return $this->rawSql($this->grammar->compileInsert($this, $values), Arr::flatten($values, 1));
        });
        \Illuminate\Database\Eloquent\Builder::macro('insertToRawSql', function ($values) {
            return $this->getQuery()->insertToRawSql($values);
        });
    }
}
