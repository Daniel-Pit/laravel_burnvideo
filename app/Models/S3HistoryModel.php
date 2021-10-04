<?php
namespace burnvideo\Models;

use Eloquent;

class S3HistoryModel extends Eloquent {
    protected $table = 's3history';
    protected $primaryKey = 'id';
    protected $fillable = ['from', 'to', 'executed_date', 'status'];
}