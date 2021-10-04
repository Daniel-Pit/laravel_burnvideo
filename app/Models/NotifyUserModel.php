<?php

namespace burnvideo\Models;

use Eloquent;

class NotifyUserModel extends Eloquent {
    protected $table = 'notification_user';
    protected $fillable = ['nid', 'uid', 'sendflag'];
}