<?php
/*
Plugin Name: PRyC WP: Comment BackEnd Note
Description: Plugin add backend note fild to all comment
Author: PRyC
Version: 1.2.5
Author URI: http://PRyC.eu
*/


if ( ! defined( 'ABSPATH' ) ) exit;
	
	
/* Save - add filter */
add_filter( 'comment_edit_redirect',  'pryc_wp_comment_note__save', 10, 2 );

/* Field - add action */
add_action( 'add_meta_boxes', 'pryc_wp_comment_note__add_box' );

/* Note field */
function pryc_wp_comment_note__add_box() {
    add_meta_box( 'pryc_wp_comment_note__section_id', __( 'Note' ), 'pryc_wp_comment_note__note_field', 'comment', 'normal' );
}

/* Show Note field */
function pryc_wp_comment_note__note_field( $comment ) {

    wp_nonce_field( plugin_basename( __FILE__ ), 'pryc_wp_comment_note__nonce' );

    $note_content = get_comment_meta( $comment->comment_ID, 'pryc_wp_comment_note__comment_meta', true );
	
	echo "<textarea id='pryc_wp_comment_note__comment_meta' name='pryc_wp_comment_note__comment_meta' cols='50' rows='5' style='width:100%' >" . esc_textarea( $note_content )  . "</textarea>";

	 
	$comment_save_time_content = get_comment_meta( $comment->comment_ID, 'pryc_wp_comment_note__comment_save_time', true );
	echo "Last comment save time: " . esc_attr( $comment_save_time_content );
	echo '<br /><br />';
	echo '<a href="http://cdn.pryc.eu/add/link/?link=paypal-wp-plugin-pryc-wp-comment-backend-note" target="_blank">' . __( 'Like my plugin? Give for a tidbit for my dogs :-)', 'pryc_wp_antyspam' ) . '</a>';	
	
}

/* Save note */
function pryc_wp_comment_note__save( $location, $comment_id ) {
    if ( !wp_verify_nonce( $_POST['pryc_wp_comment_note__nonce'], plugin_basename( __FILE__ ) ) && !isset( $_POST['pryc_wp_comment_note__comment_meta'] ) ) {
        return $location;
	}

	update_comment_meta( $comment_id, 'pryc_wp_comment_note__comment_meta', implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['pryc_wp_comment_note__comment_meta'] ) ) ));
	
	$comment_save_time = current_time( 'mysql' );
	update_comment_meta( $comment_id, 'pryc_wp_comment_note__comment_save_time', sanitize_text_field( $comment_save_time ) );
    return $location;
}

/* Add note content to comment cloumn */

function pryc_wp_comment_note__add_note_column( $pryc_note_columns )
{	
	$pryc_note_columns['pryc_comment_note_columns'] = __( 'Note' );
	return $pryc_note_columns;
}
add_filter( 'manage_edit-comments_columns', 'pryc_wp_comment_note__add_note_column' );

function pryc_wp_comment_note__column_note_content( $pryc_note_column, $comment_ID )
{
	if ( 'pryc_comment_note_columns' == $pryc_note_column ) {		
		$comment_note_content = get_comment_meta( $comment_ID, 'pryc_wp_comment_note__comment_meta', true );
			if ( $comment_note_content ) {
			#echo '<a class="pryc_column_note_content tooltip" data-tip="DATA TIP" href="#">Yes</a>';
			echo '<a class="pryc_column_note_content tooltip" data-tip="' . $comment_note_content  . '" href="' . get_site_url() . '/wp-admin/comment.php?action=editcomment&c=' . $comment_ID . '">Note</a>';
			}
	}
}
add_filter( 'manage_comments_custom_column', 'pryc_wp_comment_note__column_note_content', 10, 2 );

/* Admin CSS */
function pryc_wp_comment_note_css() {
?>
	<style type="text/css">
		.column-pryc_comment_note_columns { width: 35px; }
	

		a.pryc_column_note_content.tooltip {
			position: relative;
		}

		a.pryc_column_note_content.tooltip::before {
			content: attr(data-tip);
			position: absolute;
			z-index: 999;
			/*bottom: 9999px;*/
			background: #555;
			color: #f0f0f0;
			padding: 8px;
			/*height: 200px;*/
			width: 350px;
			top: 0px;
			left: -380px;
			display: none;
			/*opacity: 0;*/
		}

		a.pryc_column_note_content.tooltip:hover::before {
			/*opacity: 1;*/
			display: block;
		}
	</style>
<?php
}
add_action('admin_head', 'pryc_wp_comment_note_css');
