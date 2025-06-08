<?php

namespace Luna\Db;

class Log extends Model
{
    const UPDATED_AT = null;
    
    protected $fillable = [
        "code",
        "message",
        "public_id"
    ];

    public $timestamps = [
        "created_at"
    ];
}