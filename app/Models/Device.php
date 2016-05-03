<?php

namespace App\Models;

class Device extends Model
{
    /**
     * The supported services for push notifications.
     *
     * @var array
     */
    public static $services = [
        'apns',
        'gcm',
        'web',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'service',
        'device_uuid',
        'device_token',
    ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'service',
    ];

    /**
     * Relation with user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
