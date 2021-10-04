<?php
namespace burnvideo\Models;

use Eloquent;
class MessageUserModel extends Eloquent {

    protected $table = 'message_user';
    protected $fillable = ['mid', 'uid', 'sendflag'];
}