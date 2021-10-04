<?php
namespace burnvideo\Models;
use Eloquent;

class MessageModel extends Eloquent {

    protected $table = 'message';
    protected $fillable = ['mid', 'm_sender', 'm_title', 'm_message', 'm_sendtype', 'm_time'];
}