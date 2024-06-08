<?php
/*
Plugin Name: WooCommerce Custom Schema Integration
Description: Replace the default JSON-LD schema on WooCommerce product detail pages with custom schema format.
Version: 1.0
Author: Quang Minh - Dev
*/

// Remove Yoast Schema on WooCommerce product detail page
add_action('template_redirect', 'devvn_remove_yoast_schema_on_product_page', 20);
function devvn_remove_yoast_schema_on_product_page()
{
    if (is_product()) {
        add_filter('wpseo_json_ld_output', '__return_false', 20); // Disable Yoast schema output with a lower priority
    }
}

// Function to get the product name
function get_product_name($product)
{
    return $product ? $product->get_name() : '';
}

// Function to get the product URL
function get_product_url()
{
    return get_permalink();
}

// Function to get keywords from Yoast SEO
function get_product_keywords()
{
    return get_post_meta(get_the_ID(), '_yoast_wpseo_focuskw', true);
}

// Function to get product images
function get_product_images($product)
{
    $images = [];
    if ($product) {
        $images[] = wp_get_attachment_url($product->get_image_id());
        foreach ($product->get_gallery_image_ids() as $image_id) {
            $images[] = wp_get_attachment_url($image_id);
        }
    }
    return $images;
}

// Function to get the product meta description
function get_product_meta_description()
{
    return get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true);
}

// Function to get the product SKU
function get_product_sku($product)
{
    if ($product) {
        $sku = $product->get_sku();
        if (empty($sku)) {
            $random_number = rand(1000, 9999);
            $sku = 'TDR' . $random_number;
        }
        return $sku;
    }
    return '';
}

// Function to get the product MPN (assuming MPN is stored in a meta field 'mpn')
function get_product_mpn($product)
{
    if ($product) {
        $mpn = get_post_meta($product->get_id(), 'mpn', true);
        if (empty($mpn)) {
            $random_number = rand(1000, 9999);
            $mpn = 'TDR' . $random_number;
        }
        return $mpn;
    }
    return '';
}

// Function to return JSON for hasMerchantReturnPolicy field
function get_hasMerchantReturnPolicy()
{
    return '{
        "@type": "MerchantReturnPolicy",
        "inStoreReturnsOffered": "True",
        "merchantReturnDays": "10",
        "merchantReturnLink": "https://viettienplastic.vn/chinh-sach-doi-tra/"
    }';
}

// Function to return JSON for shippingDetails field
function get_shippingDetails()
{
    return '{
        "@type": "OfferShippingDetails",
        "shippingRate": {
            "@type": "MonetaryAmount",
            "value": "0",
            "currency": "VND"
        },
        "shippingDestination": {
            "@type": "DefinedRegion",
            "addressCountry": "VN"
        },
        "deliveryTime": {
            "@type": "ShippingDeliveryTime",
            "businessDays": "3-5"
        },
        "shippingLabel": "Nhựa Việt Tiến Tự Vận Chuyển"
    }';
}

// Function to get random reviews from the JSON data
function get_random_reviews()
{
    $reviews_data = [
        ["Huy", "Nguyễn", "Huy Nguyễn", "5", "Còn hàng không shop ơi?"],
        ["Khang", "Minh", "Minh Khang", "4", "Bên mình có hỗ trợ trả góp không ạ?"],
        ["Trang", "Hà", "Hà Trang", "5", "Tôi muốn mua vận chuyển về Long An thì phí ship sao?"],
        ["Linh", "Phạm", "Phạm Linh", "4", "Bây giờ mình mua thì có khuyến mãi giảm giá gì không ạ?"],
        ["Phương", "Trần", "Phương Trần", "5", "Tôi cần tư vấn về sản phẩm"],
        ["Minh", "Nguyễn", "Nguyễn Minh", "5", "Giao hàng nhanh, đóng gói chắc chắn, nhân viên hỗ trợ nhiệt tình. Chắc chắn sẽ giới thiệu cho bạn bè mua ở đây."],
        ["Khoa", "Việt", "Việt Khoa", "4", "Sản phẩm OK nha"],
        ["Phát", "Lê", "Lê Phát", "5", "Rất tốt ạ"],
        ["Khôi", "Huỳnh", "Huỳnh Khôi", "4", "Sẽ giới thiệu sản phẩm cho bạn bè, người thân. Chiêm Tài làm ăn OK."],
        ["Anh", "Nam", "Nam Anh", "5", "Hài lòng. Sản phẩm rất ổn trong tầm giá. Sẽ giới thiệu người thân sử dụng luôn."],
        ["Hương", "Bùi", "Hương Bùi", "4", "Đợt nghe người bạn giới thiệu mua dùng, cũng được nửa năm rồi mà ổn áp, không lỗi vặt, dễ sử dụng"],
        ["Thảo", "Đỗ", "Thảo Đỗ", "5", "Hàng xài rồi mà ok lắm ạ, thương hiệu lạ lúc mua cũng hơi lấn cấn mà mua rồi thấy ổn áp lắm ạ"],
        ["Hà", "Ngô", "Ngô Hà", "4", "Hàng chất lượng và đẹp."],
        ["Huyền", "Dương", "Dương Huyền", "5", "Sản phẩm chất lượng và dùng rất OK theo cảm nhận của mình."],
        ["Ngọc", "Lam", "Lam Ngọc", "5", "OK"],
        ["Hằng", "Phạm", "Hằng Phạm", "4", "Rất hài lòng"],
        ["Ngân", "Kim", "Kim Ngân", "5", "Dùng ổn nè, sẽ tiếp tục ủng hộ hãng"],
        ["Quang", "Anh", "Anh Quang", "4", "Tốt"],
        ["Thạch", "Ngọc", "Ngọc Thạch", "5", "OK"],
        ["Nam", "Lý", "Lý Nam", "5", "Tốt, ủng hộ"]
    ];

    shuffle($reviews_data); // Shuffle the reviews to get random ones
    $random_reviews = array_slice($reviews_data, 0, 5); // Get up to 5 random reviews
    $reviews = [];
    foreach ($random_reviews as $review) {
        $reviews[] = [
            "@type" => "Review",
            "name" => $review[2],
            "reviewBody" => $review[4],
            "reviewRating" => [
                "@type" => "Rating",
                "ratingValue" => $review[3],
                "bestRating" => "5",
                "worstRating" => "4"
            ],
            "datePublished" => date("Y-m-d"),
            "author" => [
                "@type" => "Person",
                "name" => $review[2]
            ]
        ];
    }
    return $reviews;
}

// Function to get product reviews or random reviews if none exist
function get_product_reviews($product)
{
    if ($product && $product->get_review_count() > 0) {
        $reviews = [];
        $comments = get_comments([
            'post_id' => $product->get_id(),
            'status' => 'approve',
            'number' => 5 // Limit to 5 reviews
        ]);
        foreach ($comments as $comment) {
            $reviews[] = [
                "@type" => "Review",
                "name" => $comment->comment_author,
                "reviewBody" => $comment->comment_content,
                "reviewRating" => [
                    "@type" => "Rating",
                    "ratingValue" => get_comment_meta($comment->comment_ID, 'rating', true),
                    "bestRating" => "5",
                    "worstRating" => "1"
                ],
                "datePublished" => $comment->comment_date,
                "author" => [
                    "@type" => "Person",
                    "name" => $comment->comment_author
                ]
            ];
        }
        return $reviews;
    } else {
        return get_random_reviews();
    }
}

// Function to get highPrice, lowPrice, and offerCount for products in the same category
function get_price_info($product)
{
    $categories = $product->get_category_ids();
    if (empty($categories)) {
        return [
            'highPrice' => 0,
            'lowPrice' => 0,
            'offerCount' => 0
        ];
    }

    $args = [
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => [
            [
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $categories,
            ],
        ],
    ];

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return [
            'highPrice' => 0,
            'lowPrice' => 0,
            'offerCount' => 0
        ];
    }

    $prices = [];
    foreach ($query->posts as $post) {
        $product_in_cat = wc_get_product($post->ID);
        if ($product_in_cat) {
            $price = $product_in_cat->get_price();
            if ($price !== '') {
                $prices[] = $price;
            }
        }
    }

    wp_reset_postdata();

    if (empty($prices)) {
        return [
            'highPrice' => 0,
            'lowPrice' => 0,
            'offerCount' => 0
        ];
    }

    $high_price = max($prices);
    $low_price = min($prices);
    $offer_count = count($prices);

    return [
        'highPrice' => $high_price,
        'lowPrice' => $low_price,
        'offerCount' => $offer_count,
    ];
}

// Function to get the product material from WooCommerce attributes
function get_product_material($product)
{
    $material = $product->get_attribute('pa_nguyen-lieu');
    return $material ? $material : '';
}

// Add custom schema on WooCommerce product detail page
add_action('wp_head', 'devvn_add_custom_schema_on_product_page', 5); // High priority to add custom schema first
function devvn_add_custom_schema_on_product_page()
{
    if (is_product()) {
        global $product;

        // Create the new schema
        $custom_schema = [
            "@context" => "https://schema.org/",
            "@graph" => [
                [
                    "@type" => "Organization",
                    "@id" => "https://viettienplastic.vn/#organization",
                    "url" => "https://viettienplastic.vn/",
                    "name" => ["Nhựa Việt Tiến", "Việt Tiến Plastic", "Công ty nhựa Việt Tiến"]
                ],
                [
                    "@type" => "Person",
                    "@id" => "https://viettienplastic.vn/author/viettien#person",
                    "url" => "https://viettienplastic.vn/author/viettien",
                    "name" => "BÙI THANH DUY"
                ],
                [
                    "@type" => "WebSite",
                    "name" => ["Nhựa Việt Tiến", "Việt Tiến Plastic", "Công ty nhựa Việt Tiến"],
                    "inLanguage" => "vi-VN",
                    "description" => "Nhựa Việt Tiến chuyên sản xuất và phân phối sản phẩm thùng nhựa công nghiệp, Khay nhựa, thùng rác nhựa mẫu mã đa dạng, đảm bảo chất lượng, giá cạnh tranh",
                    "@id" => "https://viettienplastic.vn/#website",
                    "url" => "https://viettienplastic.vn/",
                    "image" => "https://viettienplastic.vn/wp-content/uploads/2019/03/viettienplastic-logo.png",
                    "copyrightHolder" => [
                        "@id" => "https://viettienplastic.vn/#organization"
                    ],
                    "author" => [
                        "@id" => "https://viettienplastic.vn/author/viettien#person"
                    ],
                    "publisher" => [
                        "@id" => "https://viettienplastic.vn/#organization"
                    ]
                ],
                [
                    "@type" => "Product",
                    "name" => get_product_name($product),
                    "url" => get_product_url(),
                    "@id" => get_product_url() . "#product",
                    "keywords" => get_product_keywords(),
                    "image" => get_product_images($product),
                    "description" => get_product_meta_description(),
                    "disambiguatingDescription" => wp_trim_words(get_the_content(), 200, '...'),
                    "brand" => [
                        "@type" => "Brand",
                        "name" => ["Nhựa Việt Tiến", "Việt Tiến Plastic", "Công ty nhựa Việt Tiến"]
                    ],
                    "sku" => get_product_sku($product),
                    "mpn" => get_product_mpn($product),
                    "material" => get_product_material($product),
                    "hasMerchantReturnPolicy" => json_decode(get_hasMerchantReturnPolicy(), true),
                    "offers" => [
                        "@type" => "AggregateOffer",
                        "url" => get_product_url(),
                        "priceCurrency" => "VND",
                        "price" => $product ? $product->get_price() : 0,
                        "highPrice" => 0,
                        "lowPrice" => 0,
                        "offerCount" => 0,
                        "itemCondition" => "https://schema.org/NewCondition",
                        "availability" => "https://schema.org/InStock",
                        "shippingDetails" => json_decode(get_shippingDetails(), true)
                    ],
                    "aggregateRating" => [
                        "@type" => "AggregateRating",
                        "ratingValue" => "5",
                        "bestRating" => "5",
                        "worstRating" => "4",
                        "ratingCount" => "217",
                        "reviewCount" => "137"
                    ],
                    "audience" => [
                        "@type" => "Audience",
                        "audienceType" => "consumer",
                        "url" => "https://en.wikipedia.org/wiki/Consumer",
                        "name" => "Consumer",
                        "geographicArea" => [
                            "@type" => "AdministrativeArea",
                            "url" => "https://vi.wikipedia.org/wiki/Việt_Nam",
                            "@id" => "kg:/m/01crd5",
                            "name" => "Việt Nam"
                        ]
                    ],
                    "review" => get_product_reviews($product)
                ]
            ]
        ];

        // Get price information for the category
        $price_info = get_price_info($product);
        $custom_schema["@graph"][3]["offers"]["highPrice"] = $price_info["highPrice"] ?? 0;
        $custom_schema["@graph"][3]["offers"]["lowPrice"] = $price_info["lowPrice"] ?? 0;
        $custom_schema["@graph"][3]["offers"]["offerCount"] = $price_info["offerCount"] ?? 0;

        // Remove material field if not set
        if (empty($custom_schema["@graph"][3]['material'])) {
            unset($custom_schema["@graph"][3]['material']);
        }

        // Add the new schema to the header
        echo '<script type="application/ld+json">' . json_encode($custom_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}
?>