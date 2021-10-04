<?php
namespace burnvideo\Models;

use Eloquent;

class Setting extends Eloquent {

    protected $table = 'settings';
    protected $fillable = ['sid', 's_name', 's_value'];
}
