<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            // Social media links
            $table->string('facebook_url')->nullable()->after('footer_text');
            $table->string('twitter_url')->nullable()->after('facebook_url');
            $table->string('instagram_url')->nullable()->after('twitter_url');
            $table->string('linkedin_url')->nullable()->after('instagram_url');
            $table->string('youtube_url')->nullable()->after('linkedin_url');
            $table->string('whatsapp_number')->nullable()->after('youtube_url');

            // About section fields
            $table->text('about_description')->nullable()->after('whatsapp_number');
            $table->string('about_image')->nullable()->after('about_description');

            // Contact section customizations
            $table->string('contact_email')->nullable()->after('about_image');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->text('contact_address')->nullable()->after('contact_phone');
            $table->text('map_embed_url')->nullable()->after('contact_address');

            // Hero section
            $table->string('hero_title')->nullable()->after('map_embed_url');
            $table->text('hero_subtitle')->nullable()->after('hero_title');
            $table->string('hero_image')->nullable()->after('hero_subtitle');

            // System description for homepage
            $table->text('system_description')->nullable()->after('hero_image');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url',
                'twitter_url',
                'instagram_url',
                'linkedin_url',
                'youtube_url',
                'whatsapp_number',
                'about_description',
                'about_image',
                'contact_email',
                'contact_phone',
                'contact_address',
                'map_embed_url',
                'hero_title',
                'hero_subtitle',
                'hero_image',
                'system_description',
            ]);
        });
    }
};