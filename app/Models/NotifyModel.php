<?php

namespace burnvideo\Models;

use Eloquent;

class NotifyModel extends Eloquent {
    protected $table = 'notification';
    protected $fillable = ['nid', 'n_title', 'n_message', 'n_sendtype', 'n_time'];
}