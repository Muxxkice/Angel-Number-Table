<?php
/*
    Plugin Name: Custom Table Plugin with Tags
    Description: エンジェルナンバーのテーブル作成を自動化するためのプラグイン。記事につけられた#数字のタグを元にテーブルを生成します。
    Version: 1.0
    Author: mao kurihara
 */

// プラグイン有効化時にエンジェルナンバーの初期設定を行う
register_activation_hook(__FILE__, function() {
    if (!get_option('angel_numbers')) {
        update_option('angel_numbers', []);
    }
});

add_action('admin_menu', function() {
    add_menu_page(
        '管理メニュータイトル',
        'Custom Table Plugin with Tags',
        'manage_options',
        'test_top_menu',
        'menu_contents',
        'dashicons-calendar',
        0
    );
});

function menu_contents() {
    include(plugin_dir_path(__FILE__) . 'form-content.php');
}

// オプションを登録
add_action('admin_init', function() {
    register_setting('angel_number_settings', 'angel_numbers');
});

// フォームのデータを処理
add_action('admin_post_save_angel_numbers', function() {
    // ユーザーが正しいセキュリティ nonce を使用して別の管理ページから参照されたことを確認
    check_admin_referer('save_angel_number_action');

    $new_number = sanitize_text_field($_POST['new_angel_number']);
    $numbers = get_option('angel_numbers', []);
    if (!empty($new_number) && is_numeric($new_number)) {
        $numbers[] = $new_number;
        update_option('angel_numbers', $numbers);
        wp_redirect(add_query_arg('message', '1', $_POST['_wp_http_referer']));
        exit;
    } else {
        wp_redirect(add_query_arg('message', '2', $_POST['_wp_http_referer']));
        exit;
    }
});


// エンジェルナンバーの桁数ごとの配列に分けた二重配列を作成
function split_numbers_by_digits($numbers)
{
    $result = [];

    foreach ($numbers as $number) {
        $digit_count = strlen($number);

        if (!isset($result[$digit_count])) {
            $result[$digit_count] = [];
        }

        $result[$digit_count][] = $number;
    }
    ksort($result);

    return $result;
}

// エンジェルナンバー表の作成
function generate_angel_number_table($numbers)
{
    $result = '';
    $list = split_numbers_by_digits($numbers);

    foreach ($list as $numbers) {
        $html = '<table class="stroke_table">';
        $counter = 0;
        sort($numbers);
        foreach ($numbers as $number) {
            if ($counter % 5 == 0) {
                $html .= '<tr>';
            }
            $html .= '<td><a href="https://rensa.jp.net/angelnumber-' . $number . '">' . $number . '</a></td>';
            if ($counter % 5 == 4) {
                $html .= '</tr>';
            }
            $counter++;
        }

        // 最後の行を閉じる
        if ($counter % 5 != 0) {
            // 不足しているセルを追加
            $remaining = 5 - ($counter % 5);
            for ($i = 0; $remaining < 5; $i++) {
                $html .= '<td class="empty-cell"></td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        $result .= $html;
    }
    return $result;
}

// ショートコードを登録
function custom_table_with_angel_number_tags_shortcode()
{
    $numbers = [];
    $paged = 1;
    $posts_per_page = 100; // 1度に取得する投稿の数

    // 数字のタグから表にする数字のリストを作成
    while (true) {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged
        );
        $query = new WP_Query($args);
        if (!$query->have_posts()) {
            break;
        }

        while ($query->have_posts()) {
            $query->the_post();
            $post_tags = get_the_tags();
            if ($post_tags) {
                foreach ($post_tags as $tag) {
                    if (ctype_digit($tag->name)) {
                        $numbers[] = $tag->name;
                    }
                }
            }
        }

        $paged++;
        wp_reset_postdata();
    }

    // 数字をユニークにし、ソート
    $numbers = array_unique($numbers);
    sort($numbers);

    // エンジェルナンバー表の作成
    return generate_angel_number_table($numbers);
}
add_shortcode('custom_table_with_angel_number_tags', 'custom_table_with_angel_number_tags_shortcode');
