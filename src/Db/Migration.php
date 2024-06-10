<?php
namespace Luna\Db;

class Migration extends Model {
    protected $fillable = ["filename", "batch"];
    public $timestamps = false;
}