<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesBlog extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

        Schema::create('blog_categories', function(Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('slug', 512);
            $table->integer('posts_num')->default(0);

            $table->timestamps();
        });

        Schema::create('blog_options', function(Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('value');

            $table->timestamps();
        });

		Schema::create('blog_posts', function(Blueprint $table) {
            $table->increments('id');

            $table->string('title', 512);
            $table->string('slug', 512);

            $table->string('image', 512)->nullable();

            $table->text('chapo')->nullable();
            $table->text('content');

            $table->integer('category_id');

            $table->enum('post_status', ['draft', 'published'])->default('published');

            //$table->timestamp('published_at')->default(\DB::raw('NOW()'));
            $table->timestamp('published_at')->useCurrent();;
            $table->timestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('blog_posts');
		Schema::drop('blog_options');
		Schema::drop('blog_categories');
	}

}
