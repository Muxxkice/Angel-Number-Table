<div class="wrap">
  <h1>エンジェルナンバーの管理</h1>
  <?php
    settings_fields('angel_number_settings');
    do_settings_sections('angel_number_settings');
    $numbers = get_option('angel_numbers', []);
              echo '<p>取得したデータ。値: ' . esc_html(var_export($numbers, true)) . '</p>';
  ?>

  <form method="post" action="admin-post.php?action=save_angel_numbers">
    <?php wp_nonce_field('save_angel_number_action', '_wpnonce_save_angel_number'); ?>
    <table>
      <tr><label>エンジェルナンバーの追加</label></tr>
      <tr><input type="text" name="new_angel_number" value="" /></tr>
    </table>
    <input type="hidden" name="posted" value="save_angel_number">
    <input type="submit" name="add_angel_number" class="button button-primary" value="追加" />
  </form>

</div>