<?php
namespace burnvideo\Models;

use Eloquent;

class CalendarEventModel extends Eloquent {

    protected $table = 'calendar_event';
    protected $fillable = [ 'uid', 'title', 'start', 'end', 'created_at', 'updated_at' ];
    
}