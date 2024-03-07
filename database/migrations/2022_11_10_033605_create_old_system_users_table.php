<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOldSystemUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('old_system_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('old_id')->nullable();
            $table->text('author_mail')->nullable();
            $table->text('title')->nullable();
            $table->text('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->text('date')->nullable();
            $table->text('post_type')->nullable();
            $table->text('permalink')->nullable();
            $table->text('image_url')->nullable();
            $table->text('image_title')->nullable();
            $table->text('image_caption')->nullable();
            $table->text('image_description')->nullable();
            $table->text('image_alt_text')->nullable();
            $table->text('image_featured')->nullable();
            $table->text('attachment_url')->nullable();
            $table->text('packages_badges')->nullable();
            $table->text('specialization')->nullable();
            $table->text('categories')->nullable();
            $table->text('hourly_rate_filter')->nullable();
            $table->text('skills')->nullable();
            $table->text('locations')->nullable();
            $table->text('languages')->nullable();
            $table->text('freelancer_english_level')->nullable();
            $table->text('freelancer_type')->nullable();
            $table->text('is_verified')->nullable();
            $table->text('hourly_rate_settings')->nullable();
            $table->text('user_type')->nullable();
            $table->text('employees')->nullable();
            $table->text('followers')->nullable();
            $table->text('per_hour_rate')->nullable();
            $table->text('rating_filter')->nullable();
            $table->text('freelancer_type_2')->nullable();
            $table->text('featured_timestamp')->nullable();
            $table->text('english_level')->nullable();
            $table->text('have_avatar')->nullable();
            $table->text('profile_health_filter')->nullable();
            $table->text('tag_line')->nullable();
            $table->text('address')->nullable();
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();
            $table->text('fw_options')->nullable();
            $table->text('profile_blocked')->nullable();
            $table->text('project_notification')->nullable();
            $table->text('linked_profile')->nullable();
            $table->text('expiry_string')->nullable();
            $table->text('skills_names')->nullable();
            $table->text('profile_strength')->nullable();
            $table->text('identity_verified')->nullable();
            $table->text('gender')->nullable();
            $table->text('country')->nullable();
            $table->text('department')->nullable();
            $table->text('yoast_wpseo_primary_languages')->nullable();
            $table->text('yoast_wpseo_content_score')->nullable();
            $table->text('yoast_wpseo_estimated_reading_time_minutes')->nullable();
            $table->text('skills_2')->nullable();
            $table->text('experience')->nullable();
            $table->text('educations')->nullable();
            $table->text('awards')->nullable();
            $table->text('projects')->nullable();
            $table->text('max_price')->nullable();
            $table->text('wrc_extra_meta')->nullable();
            $table->text('wrc_extra_meta_user_email')->nullable();
            $table->text('wrc_extra_meta_primary_skills')->nullable();
            $table->text('wrc_extra_meta_secondary_skills')->nullable();
            $table->text('invitation_count')->nullable();
            $table->text('verification_attachments')->nullable();
            $table->text('wp_old_slug')->nullable();
            $table->text('saved_projects')->nullable();
            $table->text('following_employers')->nullable();
            $table->text('status')->nullable();
            $table->text('author_id')->nullable();
            $table->text('author_username')->nullable();
            $table->text('author_firstname')->nullable();
            $table->text('author_lastname')->nullable();
            $table->text('slug')->nullable();
            $table->text('format')->nullable();
            $table->text('template')->nullable();
            $table->text('parent')->nullable();
            $table->text('parent_slug')->nullable();
            $table->text('order')->nullable();
            $table->text('comment_status')->nullable();
            $table->text('ping_status')->nullable();
            $table->text('post_modified_date')->nullable();
            $table->text('avatar_url')->nullable();
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
        Schema::dropIfExists('old_system_users');
    }
}
