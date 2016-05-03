<?php

namespace App\Services;

use DB;

class UUID
{
    /**
     * The model to use.
     *
     * @var string
     */
    protected $model;

    /**
     * The column to generate the uuid.
     *
     * @var string
     */
    protected $columns;

    /**
     * The length of to generated uuid.
     *
     * @var int
     */
    protected $length;

    /**
     * The generated uuids.
     *
     * @var array
     */
    protected $uuids = [];

    /**
     * Construct a UUID generator.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string|array  $columns
     * @param  int  $length
     * @return void
     */
    public function __construct($model, $columns, $length = 60)
    {
        $this->model = $model;
        $this->columns = (array) $columns;
        $this->length = $length;
    }

    /**
     * Generate a uuid.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string|array  $columns
     * @param  int  $length
     * @return array
     */
    public static function generate($model, $columns, $length = 60)
    {
        return (new static($model, $columns, $length))->make();
    }

    /**
     * Generates uuids for the set table and columns.
     *
     * @return array
     */
    public function make()
    {
        foreach ($this->columns as $column) {
            $this->makeSingle($column);
        }
        
        $this->saveUuidsToModel();

        return $this->uuids;
    }

    /**
     * Generates uuid for given column.
     *
     * @param  string  $column
     * @return void
     */
    protected function makeSingle($column)
    {
        $random = $this->random();

        if ($this->randomExists($column, $random)) {
            $this->makeSingle($column);
        } else {
            $this->uuids[$column] = $random;
        }
    }

    /**
     * Generate random string.
     *
     * @return string
     */
    public function random()
    {
        return str_random($this->length);
    }

    /**
     * Determines if given random string exists in the set table column.
     *
     * @param  string  $column
     * @param  string  $random
     * @return bool
     */
    public function randomExists($column, $random)
    {
        return $this->getTable()->where($column, $random)->exists();
    }

    /**
     * Begin a new database query against the table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTable()
    {
        return DB::table($this->model->getTable());
    }

    /**
     * Sets uuids to the model columns.
     *
     * @return void
     */
    protected function saveUuidsToModel()
    {
        foreach ($this->uuids as $column => $uuid) {
            $this->model->{$column} = $uuid;
        }

        $this->model->save();
    }
}
