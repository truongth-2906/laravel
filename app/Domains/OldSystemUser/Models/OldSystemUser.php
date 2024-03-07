<?php

namespace App\Domains\OldSystemUser\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Portfolio.
 */
class OldSystemUser extends Model
{
    protected $table = 'old_system_users';
    public const PASSWORD_LENGTH = 10;
    /**
     * @var string[]
     */
    protected $fillable = [
        'old_id',
        'author_mail',
        'title',
        'content',
        'excerpt',
        'date',
        'post_type',
        'permalink',
        'image_url',
        'image_title',
        'image_caption',
        'image_description',
        'image_alt_text',
        'image_featured',
        'attachment_url',
        'packages_badges',
        'specialization',
        'categories',
        'hourly_rate_filter',
        'skills',
        'locations',
        'languages',
        'freelancer_english_level',
        'freelancer_type',
        'is_verified',
        'hourly_rate_settings',
        'user_type',
        'employees',
        'followers',
        'per_hour_rate',
        'rating_filter',
        'freelancer_type_2',
        'featured_timestamp',
        'english_level',
        'have_avatar',
        'profile_health_filter',
        'tag_line',
        'address',
        'latitude',
        'longitude',
        'fw_options',
        'profile_blocked',
        'project_notification',
        'linked_profile',
        'expiry_string',
        'skills_names',
        'profile_strength',
        'identity_verified',
        'gender',
        'country',
        'department',
        'yoast_wpseo_primary_languages',
        'yoast_wpseo_content_score',
        'yoast_wpseo_estimated_reading_time_minutes',
        'skills_2',
        'experience',
        'educations',
        'awards',
        'projects',
        'max_price',
        'wrc_extra_meta',
        'wrc_extra_meta_user_email',
        'wrc_extra_meta_primary_skills',
        'wrc_extra_meta_secondary_skills',
        'invitation_count',
        'verification_attachments',
        'wp_old_slug',
        'saved_projects',
        'following_employers',
        'status',
        'author_id',
        'author_username',
        'author_firstname',
        'author_lastname',
        'slug',
        'format',
        'template',
        'parent',
        'parent_slug',
        'order',
        'comment_status',
        'ping_status',
        'post_modified_date',
        'avatar_url'
    ];
}
