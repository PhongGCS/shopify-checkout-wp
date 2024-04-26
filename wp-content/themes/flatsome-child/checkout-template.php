<?php
/*
Template name: Shopify Checkout
*/
?>

<?php get_template_part( 'shopify/layouts', 'header' ); ?>
<?php

$cart_items = WC()->cart->get_cart();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
    $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
    $company =  isset($_POST['company']) ? $_POST['company'] : '';
    $apartment = isset($_POST['apartment']) ? $_POST['apartment'] : '';
    $code = isset($_POST['code']) ? $_POST['code'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $city = isset($_POST['city']) ? $_POST['city'] : '';
   
    $line_items = [];
    foreach ($cart_items as $cart_item) {
        $product = $cart_item['data'];  // Lấy dữ liệu sản phẩm
        $product_id = $product->get_id();  // ID của sản phẩm
        $quantity = $cart_item['quantity'];  // Số lượng trong giỏ hàng
    
        // Thêm sản phẩm và số lượng vào mảng dòng sản phẩm
        $line_items[] = [
            "product_id" => $product_id,
            "quantity" => $quantity
        ];
    }
    $order_data = [
        "payment_method" => "bacs",
        "payment_method_title" => "Direct Bank Transfer",
        "set_paid" => true,
        "billing" => [
            "first_name" => $first_name,
            "last_name" => $last_name,
            "address_1" => $apartment,
            "city" => $city,
            "state" => "CA",
            "postcode" => "94103",
            "country" => "VN",
            "email" => $email,
            "phone" => $phone
        ],
        "shipping" => [
            "first_name" => $first_name,
            "last_name" => $last_name,
            "address_1" => $apartment,
            "city" => $city,
            "state" => "CA",
            "postcode" => "94103",
            "country" => "VN",
        ],
        "line_items" => $line_items
        
    ]; 
        $consumer_key = 'ck_894b00cc339ab33ff13f3e8a790f6f8608d8c047'; // Thay thế bằng khóa người tiêu dùng của bạn
        $consumer_secret = 'cs_128ce541772b4ceafe6ca7ac66cad65e1d78b950'; // Thay thế bằng bí mật người tiêu dùng của bạn
        // URL của WooCommerce REST API để tạo đơn hàng
        $api_url = 'http://new-checkout.local/wp-json/wc/v3/orders'; // Thay thế bằng URL của bạn
        // Tạo OAuth header cho xác thực
        $oauth = array(
            'oauth_consumer_key' => $consumer_key,
            'oauth_nonce' => md5(microtime()), // Giá trị ngẫu nhiên để bảo mật
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0',
        );

        $signature_base_string = 'POST&' . rawurlencode($api_url) . '&' . rawurlencode(http_build_query($oauth));
        $signature = base64_encode(hash_hmac('sha1', $signature_base_string, $consumer_secret . '&', true));
        $oauth['oauth_signature'] = $signature;

        $oauth_header = 'OAuth ' . implode(', ', array_map(function ($k, $v) {
            return $k . '="' . rawurlencode($v) . '"';
        }, array_keys($oauth), $oauth));

        // Thiết lập dữ liệu yêu cầu POST
        $request_args = array(
            'method' => 'POST',
            'headers' => array(
                'Authorization' => $oauth_header,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($order_data), // Dữ liệu đơn hàng
            'timeout' => 30,
        );

        // Gửi yêu cầu POST để tạo đơn hàng
        $response = wp_remote_post($api_url, $request_args);

        // Kiểm tra kết quả
        if (is_wp_error($response)) {
            echo 'Lỗi khi tạo đơn hàng: ' . $response->get_error_message();
        } else {
            $response_body = wp_remote_retrieve_body($response);
           
        }
}
?>
<div class="checkout-body">
    <div class="checkout-body-wrapper _flex">
        <div class="checkout-billing-info">
            <div class="billing-info-wrapper">
               <div class="express_checkout">
                    <p class="express_title">Express checkout</p>
                    <div class="express-checkout-wallets-wrapper lAHfA  ">
                        <div class="express-checkout-shop-pay">
                            <div class="shop-pay">
                                <a id="shop-pay-button" href="https://crystaltomato.com/checkouts/cn/Z2NwLWFzaWEtc291dGhlYXN0MTowMUhXNENLSDJNSDBLMTBBNlZDQUQySlJXUg?payment=shop_pay&amp;redirect_source=direct_checkout_checkout" aria-label="Shop Pay" class="KVEbf"><svg xmlns="http://www.w3.org/2000/svg" fill="inherit" aria-hidden="true" preserveAspectRatio="xMidYMid" viewBox="0 0 341 80.035" class="P3VGi" style="fill: white;"><path fill-rule="evenodd" d="M227.297 0c-6.849 0-12.401 5.472-12.401 12.223v55.59c0 6.75 5.552 12.222 12.401 12.222h101.06c6.849 0 12.401-5.472 12.401-12.222v-55.59c0-6.75-5.552-12.223-12.401-12.223zm17.702 55.892v-14.09h8.994c8.217 0 12.586-4.542 12.586-11.423s-4.369-11-12.586-11h-14.788v36.513zm0-31.084h7.664c5.319 0 7.932 2.154 7.932 5.758s-2.518 5.758-7.695 5.758h-7.901zm31.796 31.833c4.417 0 7.314-1.92 8.644-5.196.38 3.65 2.613 5.523 7.457 4.26l.048-3.886c-1.948.187-2.328-.515-2.328-2.528v-9.55c0-5.617-3.752-8.94-10.686-8.94-6.84 0-10.782 3.37-10.782 9.08h5.32c0-2.714 1.947-4.353 5.367-4.353 3.609 0 5.272 1.545 5.224 4.214v1.217l-6.127.655c-6.887.749-10.686 3.324-10.686 7.818 0 3.698 2.659 7.209 8.549 7.209m1.187-4.213c-2.992 0-4.179-1.592-4.179-3.184 0-2.153 2.47-3.136 7.314-3.698l3.8-.421c-.238 4.12-3.04 7.303-6.935 7.303m32.555 5.29c-2.422 5.804-6.317 7.536-12.396 7.536h-2.613V60.48h2.803c3.324 0 4.939-1.03 6.697-3.979l-10.782-24.95h5.984l7.695 18.21 6.839-18.21h5.842z" clip-rule="evenodd"></path><path d="M29.514 35.18c-7.934-1.697-11.469-2.36-11.469-5.374 0-2.834 2.392-4.246 7.176-4.246 4.207 0 7.283 1.813 9.546 5.363.171.274.524.369.812.222l8.927-4.447a.616.616 0 0 0 .256-.864c-3.705-6.332-10.55-9.798-19.562-9.798-11.843 0-19.2 5.752-19.2 14.898 0 9.714 8.96 12.169 16.904 13.865 7.944 1.697 11.49 2.36 11.49 5.374s-2.584 4.435-7.742 4.435c-4.763 0-8.297-2.15-10.433-6.321a.63.63 0 0 0-.843-.274L6.47 52.364a.623.623 0 0 0-.278.843c3.535 7.006 10.785 10.947 20.47 10.947 12.334 0 19.787-5.658 19.787-15.088s-9.001-12.169-16.935-13.865zM77.353 16.036c-5.062 0-9.536 1.77-12.75 4.92-.203.19-.534.053-.534-.221V.622a.62.62 0 0 0-.63-.622h-11.17a.62.62 0 0 0-.63.622v62.426a.62.62 0 0 0 .63.621h11.17a.62.62 0 0 0 .63-.621V35.664c0-5.289 4.11-9.345 9.653-9.345 5.542 0 9.557 3.972 9.557 9.345v27.384a.62.62 0 0 0 .63.621h11.17a.62.62 0 0 0 .63-.621V35.664c0-11.505-7.646-19.618-18.356-19.618zM118.389 14.255c-6.065 0-11.767 1.823-15.847 4.467a.62.62 0 0 0-.202.833l4.922 8.292c.182.295.566.4.865.22a19.8 19.8 0 0 1 10.262-2.78c9.749 0 16.914 6.785 16.914 15.75 0 7.64-5.734 13.297-13.006 13.297-5.926 0-10.037-3.403-10.037-8.207 0-2.75 1.185-5.005 4.271-6.596a.607.607 0 0 0 .246-.864l-4.645-7.754a.63.63 0 0 0-.759-.264c-6.225 2.276-10.593 7.755-10.593 15.109 0 11.126 8.981 19.428 21.507 19.428 14.629 0 25.147-9.998 25.147-24.338 0-15.372-12.237-26.603-29.066-26.603zM180.098 15.952c-5.649 0-10.689 2.054-14.373 5.678a.313.313 0 0 1-.534-.22v-4.363a.62.62 0 0 0-.63-.621H153.68a.62.62 0 0 0-.63.621v62.331a.62.62 0 0 0 .63.622h11.169a.62.62 0 0 0 .631-.622v-20.44c0-.274.331-.41.533-.231 3.674 3.371 8.532 5.342 14.096 5.342 13.102 0 23.321-10.463 23.321-24.054 0-13.592-10.23-24.054-23.321-24.054zm-2.103 37.54c-7.454 0-13.103-5.848-13.103-13.582 0-7.733 5.638-13.58 13.103-13.58s13.091 5.752 13.091 13.58-5.553 13.581-13.102 13.581z"></path></svg></a>
                            </div>
                            <div class="g-pay">
                            <button type="button" aria-label="Google Pay" class="gpay-button black plain short en" id="gpay-button-online-api-id"></button>
                                
                            </div>
                        </div>
                    </div>
                    <div class="_1fragemkh _1fragemb4 _1fragemf0 _1fragem8w _1fragemhi _1fragemir"><div role="separator" class="_1fragemir mg7oix1 mg7oix9 _1fragemmj mg7oixc _1fragemkj _1fragemjf _1fragemjb mg7oixg mg7oix3"><div class="mg7oixl"><p class="_1x52f9s1 _1fragemir _1x52f9so _1fragemkv _1fragemqv"><span class="_19gi7yt0 _19gi7ytl _1fragemkv _19gi7ytb">OR</span></p></div></div></div>
                    <div class="info_checkout">
                        <div class="info_checkout_title" style="height: 40px;">
                            <p class="info_checkout_title_contact">Contact</p>
                            <p>Log in</p>
                        </div>
                        <form action="#" method="post">
                        <div>
                            <div class="input_email">
                               
                                <div class="input_email_checkout">
                                    <input name="email" class="" type="text" placeholder="Email">
                                </div>  
                                <div class="checkbox_email">
                                    <input class="checkbox_info" type="checkbox" name="checkout" id="">
                                    <p>Email me with news and offers</p>
                                </div>
                            </div>
                            <div></div>
                        </div>
                  
                            <div class="info_checkout_title " style="height: 40px;">
                                <p class="info_checkout_title_contact">Delivery</p>
                            </div>
                            <div>
                            <div class="country">
                                <div id="provinces-container"></div>
                                    <div>
                                        <label for="">
                                            <p class="label_country">Country/Region</p></label>
                                        <select name="city" id="provinces-select"></select>
                                    </div>  
                                </div>
                           
                            </div>
                            <div class="input_full_name">
                                <div class="input_firt_name">
                                    <input name="first_name" type="text" class="input_name" placeholder="First name">
                                </div>
                                <div class="input_last_name">
                                    <input  name="last_name" type="text" class="input_name" placeholder="Last name">
                                </div>
                            </div>
                            <div>
                            <div class="input_company_checkout">
                                    <input name="company" class="input_name" type="text" placeholder="Company (optional)">
                            </div>  
                            <div class="input_apartment_checkout">
                                    <input   name="apartment" class="input_name" type="text" placeholder="Apartment,suite,etc, ">
                            </div>  
                            <div class="input_apartment_checkout">
                                    <input name="code" class="input_name" type="text" placeholder="Postal code">
                            </div>  
                            <div class="input_apartment_checkout">
                                    <input name="phone" class="input_name" type="text" placeholder="Phone">
                            </div> 
                            <div class="submit_checkout">
                                    <input class="input_submit" type="submit" value="Pay now">
                            </div>   
                        </form>
                        </div>
                </div>
                   
               </div>
            </div>
        </div>
        <div class="checkout-items-info">
            <div class="items-info-wrapper">
            <?php 
                    $cart_items = WC()->cart->get_cart();
                    $total = 0;
                    foreach ($cart_items as $cart_item) {
                        $product = $cart_item['data']; // Sản phẩm
                        $quantity = $cart_item['quantity']; // Số lượng
                        $name = $product->get_name(); // Tên sản phẩm
                        $price = $product->get_price(); // Giá
                        $image_id = $product->get_image_id();

                        // Get the URL for the product's image
                        $image_url = wp_get_attachment_url($image_id);
                        $total = $total + ($price * $quantity);
                    ?>
               <div class="checkout-items-info_product">
                    <div class="product_checkout">
                        <div class="image_product_checkout">
                            <p class="quantity"><?php  echo $quantity ?></p>
                            <img src="<?php echo $image_url ?>" alt="Beyond Sun Protection" class="_1h3po424 _1fragemkh _1fragemhs _1fragemn2 _1fragemn7 _1fragemnh _1fragemnc _1fragem3w _1fragem3c _1fragem4g _1fragem2s _1fragem69 _1fragem5k _1fragem6y _1fragem4v _1fragemi7">
                        </div>
                        <div class="name_product_checkout">
                            <p><?php echo $name ?></p>
                        </div>
                    </div>
                    <div class="price_product_checkout">
                        <p><?php echo wc_price($price) ?></p>
                    </div>
               </div>
               <?php    
                    }
                    ?>
               <div class="checkout-discount_product">
                    <div class="product_discount_checkout">
                        <div class="input_product_checkout">
                            <input class="input_discount_product" type="text">
                        </div>
                       
                    </div>
                    <div class="price_product_checkout">
                        <input class="submit_discount_product" type="submit" value="Apply">
                    </div>
               </div>
               <div class="total_price_product">
                <div class="subtotal">
                    <p>Subtotal</p>
                    <p><?php echo   wc_price($total) ?></p>
                </div>
                <div class="subtotal">
                    <p>Shipping</p>
                    <p>Enter shipping address</p>
                </div>
                <div class="subtotal">
                    <p><b>Total</b></p>
                    <small>SGD <span class="price_total_lager"><b><?php  echo   wc_price($total) ?></b></span></small>
                </div>

                
               </div>
            </div>
            
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Endpoint REST API để lấy thông tin về provinces
    const apiEndpoint = '/wp-json/custom/v1/provinces/';

    // Gọi REST API để lấy dữ liệu
    fetch(apiEndpoint)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Hiển thị dữ liệu trong giao diện
            const provincesSelect = document.getElementById('provinces-select'); // ID của phần tử <select>
            
            // Chọn một tỉnh/thành phố để đánh dấu là đã chọn
            const selectedProvinceId = 1; // Ví dụ: id của tỉnh/thành phố mặc định để hiển thị

            data.provinces.forEach(province => {
                const optionElement = document.createElement('option');
              
                optionElement.value = province.Name; // Giá trị tùy chọn
                optionElement.textContent = province.Name; // Nội dung tùy chọn
                
                // Nếu là tỉnh/thành phố mặc định, đánh dấu là đã chọn
                if (province.id === selectedProvinceId) {
                    optionElement.selected = true;
                }
                
                provincesSelect.appendChild(optionElement); // Thêm tùy chọn vào <select>
            });
        })
        .catch(error => {
            console.error('Error fetching provinces:', error);
        });
});
</script>

<?php get_template_part( 'shopify/layouts', 'footer' ); ?>