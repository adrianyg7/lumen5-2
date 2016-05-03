<?php 

namespace App\Models;

use DateTime;
use Jenssegers\Date\Date;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    /**
     * Get a fresh timestamp for the model.
     *
     * @return \Carbon\Carbon
     */
    public function freshTimestamp()
    {
        return new Date;
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return \Carbon\Carbon
     */
    protected function asDateTime($value)
    {
        if ($value instanceof Date) {
            return $value;
        }

        if ($value instanceof DateTime) {
            return Date::instance($value);
        }

        if (is_numeric($value)) {
            return Date::createFromTimestamp($value);
        }

        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value)) {
            return Date::createFromFormat('Y-m-d', $value)->startOfDay();
        }

        return Date::createFromFormat($this->getDateFormat(), $value);
    }
}
