<?php 
namespace burnvideo\Models;

use Eloquent;
//use burnvideo\Models\Category as Category;

class Post extends Eloquent {
    protected $table = 'blog_posts';
    protected $fillable = ['status', 'title', 'slug', 'chapo', 'content', 'published_at', 'category_id'];

    function getUrlAttribute($value) {
        return '/blog/'.$this->slug.'/';
    }

    function getPubDateAttribute($value) {
        return $this->created_at->format('D, d M Y H:i:s O');
    }

    function scopeIsPublished($query) {
        return $query->where('published_at','!=','0000-00-00 00:00:00')->where('published_at', '<', \DB::raw('now()'));
    }

    function is_published() {
        return ($this->published_at !== null);
    }

    function Category() {
        return $this->hasOne('burnvideo\Models\Category', 'id', 'category_id');
    }

    function getImageAttribute($value) {
        if ($value == '') {
            return config('blog.default_image');
        }
        return '/img/posts/'.$value;
    }

}
