<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /** @use HasFactory<\Database\Factories\SettingFactory> */
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'site_name',
        'site_description',
        'logo',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
        'copyright_text',
        'email',
        'phone',
        'address',
        'gmaps_embed_url',
        'mentor_commission_percent',
        'privacy_policy',
        'terms_conditions',
        'hero_image',
        'hero_title',
        'hero_subtitle',
    ];
}
