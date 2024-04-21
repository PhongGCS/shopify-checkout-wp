<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_gindex_options;
function foxtool_require_google_api_index() {
    require_once( FOXTOOL_DIR . 'link/google-api/vendor/autoload.php');
}
// check link eror
function foxtool_valid_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}
// quan ly json index api
function foxtool_jsonapi_index() {
    global $foxtool_gindex_options;
    foxtool_require_google_api_index();
    $jsonStrings = array(); 
    if (is_array($foxtool_gindex_options) || is_object($foxtool_gindex_options)) {
        foreach ($foxtool_gindex_options as $key => $value) {
            if (preg_match('/^json(\d+)$/', $key, $matches)) {
                $n = $matches[1];
                $jsonStrings[] = sanitize_text_field($value);
            }
        }
    }
    if (empty($jsonStrings)) {
        return false; 
    }
    $randomIndex = array_rand($jsonStrings);
    $selectedJsonString = $jsonStrings[$randomIndex];
    $jsonObject = json_decode($selectedJsonString, true);
    if ($jsonObject === null) {
        return false;
    }
    $client = new Google_Client();
    $client->setAuthConfig($jsonObject);
    $client->addScope('https://www.googleapis.com/auth/indexing');
    return $client->authorize();
}
// xu ly index now and del index
function foxtool_index_now($urls, $action) {
    $result = [];
    $type = $action == 'delete' ? 'URL_DELETED' : 'URL_UPDATED';
    $httpClient = foxtool_jsonapi_index();
	
	if (!$httpClient) {
        $result[] = array(
            'result' => 'error',
            'error' => 'Failed to initialize Google API client'
        );
        return $result;
    }
	
    foreach ($urls as $url) {
        $data = [
            'result' => 'success'
        ];
        if (!foxtool_valid_url($url)) {
            $data['result'] = 'error';
            $data['error'] = 'Invalid URL: ' . $url;
            $result[] = $data;
            continue;
        }
        $endpoint = 'https://indexing.googleapis.com/v3/urlNotifications:publish';
        try {
            if ($action == 'get') {
                $response = $httpClient->get('https://indexing.googleapis.com/v3/urlNotifications/metadata?url=' . urlencode($url));
            } else {
                $content = json_encode([
                    'url' => $url,
                    'type' => $type
                ]);
                $response = $httpClient->post($endpoint, ['body' => $content]);
            }
            $data['body'] = (string) $response->getBody();
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $data['result'] = 'error';
            $data['error'] = 'HTTP Error ' . $statusCode . ': ' . $e->getMessage();
        } catch (\Exception $e) {
            $data['result'] = 'error';
            $data['error'] = $e->getMessage();
        }
        $result[] = $data;
    }
    return $result;
}
// Xử lý ajax index now and del index
function foxtool_index_now_callback() {
    if (!wp_verify_nonce($_POST['ajax_nonce'], 'foxtool_index_now_nonce')) {
        wp_die('Invalid nonce');
    }
    $urls = explode("\n", $_POST['url']); 
    $action = $_POST['ajax_action'];
    // Chia nhỏ mảng URL thành các phần có tối đa 200 URL mỗi phần
    $chunks = array_chunk($urls, 200);
    // Duyệt qua từng phần và xử lý
    foreach ($chunks as $chunk) {
        $result = foxtool_index_now($chunk, $action);
        foreach ($result as $item) {
            if (array_key_exists('body', $item)) {
                $data = json_decode($item['body'], true);
                if ($data && isset($data['urlNotificationMetadata'])) {
                    $latest_update = $data['urlNotificationMetadata']['latestUpdate'] ?? null;
                    $latest_remove = $data['urlNotificationMetadata']['latestRemove'] ?? null;

                    // Xác định trạng thái dựa trên dữ liệu
                    if ($latest_update || $latest_remove) {
                        $latest_update_time = strtotime($latest_update['notifyTime'] ?? '');
                        $latest_remove_time = strtotime($latest_remove['notifyTime'] ?? '');
                        if ($latest_update_time > $latest_remove_time) {
                            $url = $data['urlNotificationMetadata']['url'];
                            $status = __('Already declared', 'foxtool');
                            $time = date('Y-m-d H:i:s', $latest_update_time);
                            $class = '';
                        } else {
                            $url = $data['urlNotificationMetadata']['url'];
                            $status = __('Declaration already deleted', 'foxtool');
                            $time = date('Y-m-d H:i:s', $latest_remove_time);
                            $class = 'ft-index-del';
                        }
                        // Hiển thị thông tin
                        echo '<div class="ft-index '. $class .'">';
                        echo __('URL:', 'foxtool') .' '. $url .'<br>';
                        echo __('Status:', 'foxtool') .' '. $status .'<br>';
                        echo __('Time:', 'foxtool') .' '. $time;
                        echo '</div>';
                        foxtool_index_use_count(); // count user
                    } else {
                        echo '<div class="ft-index ft-index-er">';
                        echo __('URL: does not exist', 'foxtool');
                        echo '</div>';
                    }
                } else {
                    // Handle invalid or missing data
                    echo '<div class="ft-index ft-index-er">';
                    echo __('URL: does not exist', 'foxtool');
                    echo '</div>';
                }
            } else {
                // Handle missing 'body' key
                echo '<div class="ft-index ft-index-er">';
                echo __('URL: does not exist', 'foxtool');
                echo '</div>';
            }
        }
    }
    wp_die();
}
add_action('wp_ajax_foxtool_index_now_ajax', 'foxtool_index_now_callback');


// xy ly index status
function foxtool_index_status($urls) {
    $result = [];
    $httpClient = foxtool_jsonapi_index(); 
    foreach ($urls as $url) {
        $data = [
            'url' => $url,
            'indexed' => null,
            'latest_update' => null, 
            'latest_remove' => null, 
        ];
        if (!foxtool_valid_url($url)) {
            $data['result'] = 'error';
            $data['error'] = 'Invalid URL: ' . $url;
            $result[] = $data;
            continue;
        }
        try {
            $response = $httpClient->get('https://indexing.googleapis.com/v3/urlNotifications/metadata?url=' . urlencode($url));
            $response_body = json_decode($response->getBody(), true);
            if (isset($response_body['latestUpdate'])) {
                $data['latest_update'] = $response_body['latestUpdate'];
            }
            if (isset($response_body['latestRemove'])) {
                $data['latest_remove'] = $response_body['latestRemove'];
            }
            if (isset($response_body['latestUpdate']) || isset($response_body['latestRemove'])) {
                $data['indexed'] = 'yes'; 
            } else {
                $data['indexed'] = 'no'; 
            }
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == 404) {
                $data['indexed'] = null;
            } else {
                $data['indexed'] = 'error'; 
                $data['error'] = 'HTTP Error ' . $statusCode . ': ' . $e->getMessage();
            }
        } catch (\Exception $e) {
            $data['indexed'] = 'error';
            $data['error'] = $e->getMessage();
        }
        $result[] = $data;
    }
    return $result;
}
// ajax index status
function foxtool_index_status_callback() {
    if (!wp_verify_nonce($_POST['ajax_nonce'], 'foxtool_index_status_nonce')) {
        wp_die('Invalid nonce');
    }
    $urls = explode("\n", $_POST['url']); 
    // Chia nhỏ mảng URL thành các phần có tối đa 200 URL mỗi phần
    $chunks = array_chunk($urls, 200);
    // Duyệt qua từng phần và xử lý
    foreach ($chunks as $chunk) {
        $url_metadata = foxtool_index_status($chunk); 
        foreach ($url_metadata as $data) {
            $url = isset($data['url']) ? $data['url'] : '';
            $latest_update = isset($data['latest_update']) ? $data['latest_update'] : null;
			$latest_remove = isset($data['latest_remove']) ? $data['latest_remove'] : null;
            if ($data['indexed'] == 'yes') {
                if ($latest_update || $latest_remove) {
                    $latest_update_time = !empty($latest_update['notifyTime']) ? strtotime($latest_update['notifyTime']) : null;
					$latest_remove_time = !empty($latest_remove['notifyTime']) ? strtotime($latest_remove['notifyTime']) : null;
                    if ($latest_update_time > $latest_remove_time) {
                        $status = __('Already declared', 'foxtool');
                        $time = date('Y-m-d H:i:s', $latest_update_time);
                        $class = '';
                    } else {
                        $status = __('Declaration already deleted', 'foxtool');
                        $time = date('Y-m-d H:i:s', $latest_remove_time);
                        $class = 'ft-index-del';
                    }
                }
                // Hiển thị thông tin
                echo '<div class="ft-index '. $class .'">';
                echo __('URL:', 'foxtool') .' '. $url .'<br>';
                echo __('Status:', 'foxtool') .' '. $status .'<br>';
                echo __('Time:', 'foxtool') .' '. $time;
                echo '</div>';
                foxtool_index_use_count(); // count user
            } else {
                echo '<div class="ft-index ft-index-er">';
                echo __('URL:', 'foxtool') .' '. $url .' '. __('does not exist', 'foxtool');
                echo '</div>';
            }
        }
    }
    wp_die();
}
add_action('wp_ajax_foxtool_index_status_ajax', 'foxtool_index_status_callback');


// index post, page, product
function foxtool_index_post_title($post_id) {
	foxtool_index_use_count(); // count user
    $url = get_permalink($post_id);
    $urls = [$url]; 
    foxtool_index_now($urls, 'update');
}
// Thêm hook cho từng loại post type
if(isset($foxtool_gindex_options['posttype'])){
	$main_search_post_types = $foxtool_gindex_options['posttype'];
	foreach ($main_search_post_types as $post_type) {
		$hook_name = 'publish_' . $post_type;
		add_action($hook_name, 'foxtool_index_post_title');
	}
}

// ham cap nhat count
function foxtool_index_use_count() {
    $count = get_transient('foxtool_index_count');
    if (false === $count) {
        $count = 0;
    }
    $count++;
    set_transient('foxtool_index_count', $count, 86400);
}


