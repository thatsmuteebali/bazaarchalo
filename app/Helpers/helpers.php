<?php

if (!function_exists('getWebsiteContent')) {
    function getWebsiteContent($page)
    {
        return \App\Models\WebContent::where('page', $page)
            ->pluck('value', 'field')->toArray();
    }
}