<?php

if (! function_exists('trans')) {
    /**
     * Translate the given message.
     *
     * @param  string  $id
     * @param  array   $parameters
     * @param  string  $domain
     * @param  string  $locale
     * @return \Symfony\Component\Translation\TranslatorInterface|string
     */
    function trans($id = null, $parameters = [], $domain = 'messages', $locale = null)
    {
        if (is_null($id)) {
            return app('translator');
        }

        return app('translator')->trans($id, $parameters, $domain, $locale);
    }
}

if (! function_exists('success')) {
    /**
     * Return a new success response from the application.
     *
     * @param  string  $message
     * @param  array  $params
     * @return Response
     */
    function success($message, array $params = [])
    {
        return new App\Http\Response($message, $params);
    }
}

if (! function_exists('error')) {
    /**
     * Return a new error response from the application.
     *
     * @param  string  $message
     * @param  array  $params
     * @param  int  $status
     * @return Response
     */
    function error($message, array $params = [], $status = 400)
    {
        if ($status < 400) {
            throw new InvalidArgumentException('Status code can not be less than 400.');
        }

        return new App\Http\Response($message, $params, $status);
    }
}

if (! function_exists('now')) {
    /**
     * Get a Carbon instance for the current date and time
     *
     * @param DateTimeZone|string|null $tz
     * @return Carbon\Carbon
     */
    function now($tz = null)
    {
        return Jenssegers\Date\Date::now($tz);
    }
}
