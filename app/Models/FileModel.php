<?php
namespace burnvideo\Models;

use Eloquent;

class FileModel extends Eloquent {

    protected $table = 'file';
    protected $fillable = ['uid', 'ftype', 'furl', 'ftsurl', 'fzipurl', 'fplaytime', 'fweight', 'fstatus', 'finserttime', 'file_index', 'ct_caption'];
}