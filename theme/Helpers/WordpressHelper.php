<?php

namespace Theme\Helpers;

class WordpressHelper {
    public function isAutoSave(): bool
    {
        return defined('DOING_AUTOSAVE') && DOING_AUTOSAVE;
    }

    public function currentUrlWithParams() : string {
        // Get the protocol (http or https)
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        // Get the host (domain)
        $host = $_SERVER['HTTP_HOST'];

        // Get the request URI (the path and query string)
        $request_uri = $_SERVER['REQUEST_URI'];

        // Combine them to form the full URL
        $full_url = $protocol . $host . $request_uri;

        return $full_url;
    }

    public function addParamsToUrl(array $params, ?string $url = null) : string {
        if($url === null) {
            $url = $this->currentUrlWithParams();
        }

        // Parse the URL into its components
        $url_components = parse_url($url);

        // Extract existing query parameters from the URL, if any
        parse_str($url_components['query'] ?? '', $existing_params);

        // Merge the existing and new parameters, with new params taking precedence
        $merged_params = array_merge($existing_params, $params);

        // Rebuild the query string
        $new_query_string = http_build_query($merged_params);

        // Rebuild the full URL with the new query string
        $new_url = $url_components['scheme'] . '://' . $url_components['host'] . (isset($url_components['port']) ? ':' . $url_components['port'] : '') . $url_components['path'] . '?' . $new_query_string;

        // Append the fragment if it exists
        if (isset($url_components['fragment'])) {
            $new_url .= '#' . $url_components['fragment'];
        }

        return $new_url;
    }
}