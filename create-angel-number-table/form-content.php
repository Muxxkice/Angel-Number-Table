<div class="wrap">
  <h1>エンジェルナンバーの管理</h1>
  <?php
    settings_fields('angel_number_settings');
    do_settings_sections('angel_number_settings');
    $numbers = get_option('angel_numbers', []);
              echo '<p>取得したデータ。値: ' . esc_html(var_export($numbers, true)) . '</p>';
  ?>

  <?php if (isset($_GET['message'])) : ?>
    <?php if ($_GET['message'] == MESSAGE_SUCCESS) : ?>
        <div class="updated"><p><strong>設定を保存しました</strong></p></div>
    <?php elseif ($_GET['message'] == MESSAGE_INVALID_INPUT) : ?>
        <div class="error"><p><strong>無効な文字が含まれています。半角数字のみを入力してください。</strong></p></div>
    <?php elseif ($_GET['message'] == MESSAGE_DUPLICATE_ENTRY) : ?>
        <div class="error"><p><strong>このエンジェルナンバーは既に存在します。</strong></p></div>
    <?php endif; ?>
  <?php endif; ?>

  <form method="post" action="admin-post.php?action=save_angel_numbers">
    <?php wp_nonce_field('save_angel_number_action', '_wpnonce_save_angel_number'); ?>
    <table>
      <tr><label>エンジェルナンバーの追加</label></tr>
      <td><input type="text" name="new_angel_number" id="new_angel_number" value="" /></td>
    </table>
    <input type="submit" name="add_angel_number" id="add_angel_number" class="button button-primary" value="追加" disabled/>
  </form>

</div>

<script>
  const number = document.getElementById("new_angel_number");
  const button = document.getElementById("add_angel_number");
  number.addEventListener("input", () => {
    if(number.value){
      button.disabled = null;
    } else {
      button.disabled = "disabled";
    }
  })
</script>