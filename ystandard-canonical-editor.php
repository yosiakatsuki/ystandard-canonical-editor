<?php
/**
 * Plugin Name:     yStandard Canonical Editor
 * Plugin URI:      https://github.com/yosiakatsuki/ystandard-canonical-editor
 * Description:     ページ単位でcanonical URL を編集できるプラグイン
 * Author:          yosiakatsuki
 * Author URI:      https://yosiakatsuki.net/blog
 * Text Domain:     ystandard-canonical-editor
 * Domain Path:     /languages
 * Version:         0.0.1
 *
 * @author          yosiakatuki
 * @package         yStandard_Canonical_Editor
 * @license         GPL-2.0+
 */

/**
 * Post Meta 追加
 */
function ystdce_admin_menu() {
	/**
	 * 投稿オプション
	 */
	add_meta_box(
		'ystdce_add_option',
		'yStandard Canonical Editor',
		'ystdce_add_option',
		'post',
        'side'
	);
	add_meta_box(
		'ystdce_add_option',
		'yStandard Canonical Editor',
		'ystdce_add_option',
		'page',
		'side'
	);
}

add_action( 'admin_menu', 'ystdce_admin_menu' );


/**
 * メタボックス
 */
function ystdce_add_option() {
	?>
    <h3 class="meta-box__headline">Canonical設定</h3>
    <label for="ystdce_canonical_url">Canonical URL</label><br>
    <input type="text" name="ystdce_canonical_url" id="ystdce_canonical_url" value="<?php echo get_post_meta( get_the_ID(), '_ystdce_canonical_url', true ); ?>">
	<?php
}

/**
 * 設定保存
 *
 * @param int $post_id Post ID.
 */
function ystdce_save_post( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['ystdce_canonical_url'] ) ) {
		return;
	}
	if ( ! empty( $_POST['ystdce_canonical_url'] ) ) {
		update_post_meta(
			$post_id,
			'_ystdce_canonical_url',
			esc_url_raw( $_POST['ystdce_canonical_url'] )
		);
	} else {
		delete_post_meta( $post_id, '_ystdce_canonical_url' );
	}
}
add_action( 'save_post', 'ystdce_save_post' );

/**
 * Canonical URL 書き換え
 *
 * @param string  Canonical URL
 *
 * @return string
 */
function ystdce_the_canonical_tag( $canonical ) {

	if ( is_singular() ) {
		global $post;
		$url = get_post_meta( $post->ID, '_ystdce_canonical_url', true );
		if ( ! empty( $url ) ) {
			$canonical = $url;
		}
	}

	return $canonical;
}

add_filter( 'ys_the_canonical_tag', 'ystdce_the_canonical_tag' );