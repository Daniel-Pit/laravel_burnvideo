<?php 
namespace burnvideo\Models;

use Eloquent;

class Option extends Eloquent {
    protected $table = 'blog_options';
    protected $fillable = ['name', 'value'];

    static public function get($name) {
        $option = self::whereName($name)->first();
        if (isset($option->value)) {
            return $option->value;
        }

        // default values
        if ($name == "rss_number") {
            return 10;
        }

        return null;
    }
}
