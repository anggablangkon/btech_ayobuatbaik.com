<?php

use App\Models\SiteSetting;

/**
 * Get a site setting value by key.
 */
function site_setting(string $key, $default = null)
{
    return SiteSetting::get($key, $default);
}

/**
 * Get combined site name (highlight + rest).
 */
function site_name(): string
{
    return site_setting('site_name_highlight', 'Ayo') . site_setting('site_name_rest', 'buatbaik');
}
