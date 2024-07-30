<?php
  if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.'));
  }
?>

<div class="antg-wrap">
  <h1>エンジェルナンバーの管理</h1>
  <?php
    settings_fields('angel_number_settings');
    do_settings_sections('angel_number_settings');
    $numbers = get_option('angel_numbers', []);
  ?>

  <?php if (isset($_GET['message'])) : ?>
    <?php if ($_GET['message'] == MESSAGE_SUCCESS) : ?>
        <div class="updated"><p><strong><?php echo esc_html__('設定を保存しました', 'text-domain'); ?></strong></p></div>
    <?php elseif ($_GET['message'] == MESSAGE_INVALID_INPUT) : ?>
        <div class="error"><p><strong><?php echo esc_html__('無効な文字が含まれています。半角数字のみを入力してください。', 'text-domain'); ?></strong></p></div>
    <?php elseif ($_GET['message'] == MESSAGE_DUPLICATE_ENTRY) : ?>
        <div class="error"><p><strong><?php echo esc_html__('このエンジェルナンバーは既に存在します。', 'text-domain'); ?></strong></p></div>
    <?php endif; ?>
  <?php endif; ?>

  <form method="post" action="admin-post.php?action=save_angel_numbers">
    <?php wp_nonce_field('save_angel_number_action', '_wpnonce_save_angel_number'); ?>
    <h3>■エンジェナンバーの追加</h3>
    <table>
      <tr><label>追加したい数字(半角英数字)</label></tr>
      <td><input type="text" name="new_angel_number" id="new_angel_number" value="" /></td>
    </table>
    <input type="submit" name="add_angel_number" id="add_angel_number" class="button button-primary" value="追加" disabled/>
  </form>

  <section>
    <form method="post" action="admin-post.php?action=delete_angel_numbers">
      <h3>■エンジェナンバーの削除</h3>
      <p>削除したい数字にチェックを入れてください</p>
      <?php wp_nonce_field('delete_angel_numbers_action', '_wpnonce_delete_angel_numbers'); ?>
      <ul>
      <?php sort($numbers); ?>
      <?php foreach ($numbers as $number): ?>
          <li>
              <label>
                  <input type="checkbox" name="delete_numbers[]" value="<?php echo esc_attr($number); ?>" class="number-checkbox">
                  <?php echo esc_html($number); ?>
              </label>
          </li>
      <?php endforeach; ?>
      </ul>
      <input type="submit" id="delete_button" value="選択した数字を削除" class="button button-secondary" disabled>
    </form>
  </section>

</div>

<script>
  // 数字追加用の入力フィールドとボタンの設定
  const number = document.getElementById("new_angel_number");
  const addButton = document.getElementById("add_angel_number");
  number.addEventListener("input", () => {
    if(number.value){
      addButton.disabled = null;
    } else {
      addButton.disabled = "disabled";
    }
  })

  // チェックボックスと削除ボタンの設定
  const checkboxes = document.querySelectorAll(".number-checkbox");
  const deleteButton = document.getElementById("delete_button");

  checkboxes.forEach(checkbox => {
    checkbox.addEventListener("change", () => {
      // 少なくとも一つのチェックボックスがチェックされているかを確認
      deleteButton.disabled = !Array.from(checkboxes).some(chk => chk.checked);
    });
  });
</script>