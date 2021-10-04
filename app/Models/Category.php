<?php 
namespace burnvideo\Models;

use Eloquent;

class Category extends Eloquent {
    protected $table = 'blog_categories';
    protected $fillable = ['name', 'slug'];

    function getUrlAttribute() {
        return config('blog.base_path').$this->slug.'/';
    }
}
