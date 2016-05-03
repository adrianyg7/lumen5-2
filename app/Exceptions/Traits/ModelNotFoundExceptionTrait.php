<?php

namespace App\Exceptions\Traits;

use ReflectionClass;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ModelNotFoundExceptionTrait
{
    /**
     * Determines if given Exception is ModelNotFoundException.
     *
     * @param  \Exception  $e
     * @return bool
     */
    protected function isModelNotFoundException($e)
    {
        return $e instanceof ModelNotFoundException;
    }

    /**
     * Renders the given ModelNotFoundException.
     *
     * @param  \Illuminate\Database\Eloquent\ModelNotFoundException  $e
     * @return Response
     */
    protected function renderModelNotFoundException(ModelNotFoundException $e)
    {
        $resource = $this->getModelTransKey($e->getModel());

        $message = trans('models.not_found', [
            'resource' => trans("models.{$resource}"),
        ]);

        return error($message, [], 404);
    }

    /**
     * Renders the given ModelNotFoundException.
     *
     * @param  string  $model
     * @return string
     */
    protected function getModelTransKey($model)
    {
        $key = (new ReflectionClass($model))->getShortName();

        return strtolower($key);
    }
}