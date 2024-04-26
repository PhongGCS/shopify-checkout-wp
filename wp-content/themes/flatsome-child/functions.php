<?php
// Tạo endpoint tùy chỉnh để lấy dữ liệu từ JSON
function get_provinces_data() {
    $file_path = get_stylesheet_directory() . '/shopify/assets/json/provinces.json';
  
    if (file_exists($file_path)) {
        $json_contents = file_get_contents($file_path);
        $provinces = json_decode($json_contents, true);

        if ($provinces === null) {
            return new WP_Error('json_parse_error', 'Failed to parse JSON', array('status' => 500));
        }

        return rest_ensure_response(array('provinces' => $provinces));
    } else {
        return new WP_Error('file_not_found', 'File not found', array('status' => 404));
    }
}

add_action('rest_api_init', function() {
    register_rest_route('custom/v1', '/provinces/', array(
        'methods' => 'GET',
        'callback' => 'get_provinces_data',
    ));
});

function enqueue_my_scripts() {
    // Chèn tệp JavaScript
    wp_enqueue_script('my-custom-script', get_stylesheet_directory_uri() . '/js/script.js', array(), null, true);
}

add_action('wp_enqueue_scripts', 'enqueue_my_scripts');


