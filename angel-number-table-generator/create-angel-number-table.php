<?php
/*
    Plugin Name: Angel Number Table Generator
    Description: エンジェルナンバーのテーブル作成を自動化するためのプラグイン。
    Version: 1.0
    Author: mao kurihara
 */

require_once plugin_dir_path(__FILE__) . 'constants.php';

// プラグイン有効化時にエンジェルナンバーの初期設定を行う
register_activation_hook(__FILE__, function() {
    if (!get_option('angel_numbers')) {
        update_option('angel_numbers', []);
    }
});

add_action('admin_menu', function() {
    add_menu_page(
        '管理メニュータイトル',
        'Angel Number Table Generator',
        'manage_options',
        'top_menu',
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

// エンジェルナンバーが有効かどうかを確認する関数
function is_valid_angel_number($new_number, $existing_numbers, &$error_code = null) {
    // 半角数字かどうかをチェック
    if (!preg_match('/^[0-9]+$/', $new_number)) {
        $error_code = MESSAGE_INVALID_INPUT;
        return false;
    }

    // 重複チェック
    if (in_array($new_number, $existing_numbers)) {
        $error_code = MESSAGE_DUPLICATE_ENTRY;
        return false;
    }

    return true;
}

// フォームのデータを処理
add_action('admin_post_save_angel_numbers', function() {
    // ユーザーがセキュリティ nonce を使用して正しい管理ページから参照されたことを確認
    check_admin_referer('save_angel_number_action', '_wpnonce_save_angel_number');

    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    $new_number = sanitize_text_field($_POST['new_angel_number']);
    $numbers = get_option('angel_numbers', []);
    $error_code = null;
    $is_valid = is_valid_angel_number($new_number, $numbers, $error_code);

    if (!is_array($numbers)) {
        $numbers = []; // ここで配列に初期化
    }
    if ($is_valid) {
        $numbers[] = $new_number;
        update_option('angel_numbers', $numbers);
        wp_redirect(add_query_arg('message', MESSAGE_SUCCESS, $_POST['_wp_http_referer']));
        exit;
    } else {
        wp_redirect(add_query_arg('message',  $error_code, $_POST['_wp_http_referer']));
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
            for ($i = 0; $i < $remaining; $i++) {
                $html .= '<td class="empty-cell"></td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        $result .= $html;

        // HTML文字列の解放
        unset($html);
    }

    return $result;
}


// ショートコードを登録
function custom_table_with_angel_number_tags_shortcode()
{
    // データベースから保存されている数字のリストを取得
    $numbers = get_option('angel_numbers', []);
    // 数字をユニークにし、ソート
    $numbers = array_unique($numbers);
    sort($numbers);

    // エンジェルナンバー表の作成
    return generate_angel_number_table($numbers);
}
add_shortcode('custom_table_with_angel_number_tags', 'custom_table_with_angel_number_tags_shortcode');
