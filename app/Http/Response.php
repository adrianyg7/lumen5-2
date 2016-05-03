<?php

namespace App\Http;

use InvalidArgumentException;
use Illuminate\Http\JsonResponse;

class Response extends JsonResponse
{
    /**
     * Response parameters.
     *
     * @var array
     */
    protected $params = [];

    /**
     * Constructor of a new app response.
     *
     * @param  string  $message
     * @param  array  $params
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     */
    public function __construct($message = '', array $params = [], $status = 200, $headers = [], $options = 0)
    {
        $this->with(compact('message'))->with($params);

        parent::__construct($this->params, $status, $headers, $options);
    }

    /**
     * Initializes a success response.
     *
     * @param  string  $message
     * @param  array  $params
     * @return $this
     */
    public function success($message, array $params = [])
    {
        $this->setStatusCode(200);

        return $this->with(compact('message'))->with($params);
    }

    /**
     * Initializes an error response.
     *
     * @param  string  $message
     * @param  array  $params
     * @param  int  $status
     * @return $this
     */
    public function error($message, array $params = [], $status = 400)
    {
        if ($status < 400) {
            throw new InvalidArgumentException('Status code can not be less than 400.');
        }

        $this->setStatusCode($status);

        return $this->with(compact('message'))->with($params);
    }

    /**
     * Add a piece of data to the params.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return $this
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->params = array_merge($this->params, $key);
        } else {
            $this->params[$key] = $value;
        }

        return $this;
    }

    /**
     * Sends HTTP headers and content.
     *
     * @return Response
     */
    public function send()
    {
        $this->setData($this->params);

        parent::send();
    }
}
