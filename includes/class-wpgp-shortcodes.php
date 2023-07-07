<?php
/**
 * This class provides the support for the Plugin shortcodes and necessary files for it.
 *
 * @package wpgp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPGP_SHORTCODES class
 */
class WPGP_SHORTCODES {

	/**
	 * Form shortcode
	 *
	 * @var string
	 */
	public $wpgp_form_shortcode = 'wpgp-form';
	/**
	 * Pending posts shortcode.
	 *
	 * @var string
	 */
	public $wpgp_pending_posts_shortcode = 'wpgp-pending-posts';

	/**
	 * WPGP_SHORTCODES constructor
	 */
	public function __construct() {
		// Adds the form shortcode.
		add_shortcode( $this->wpgp_form_shortcode, array( $this, 'form_shortcode_handler' ) );
		// Adds the pending posts listing shortcode.
		add_shortcode( $this->wpgp_pending_posts_shortcode, array( $this, 'pending_posts_shortcode_handler' ) );
		// Action hook to add the scripts and styles for the shortcode.
		add_action( 'wp_enqueue_scripts', array( $this, 'shortcode_scripts' ) );
		// Action hook to handle ajax request.
		add_action( 'wp_ajax_submit_post', array( $this, 'submit_post' ) );
	}

	/**
	 * Function to handle shortcode for form
	 *
	 * @return string HTML for the form.
	 */
	public function form_shortcode_handler() {
		ob_start();
		$user  = wp_get_current_user();
		$roles = (array) $user->roles;

		if ( is_user_logged_in() && ( current_user_can( 'edit_posts' ) && in_array( 'author', $roles, true ) ) || current_user_can( 'manage_options' ) ) {
			?>
		<div class="wpgp-form-wrapper">
			<form class="wpgp-form" enctype="multipart/form-data" method="post">
				<h2><?php esc_html_e( 'Create Guest Posts', 'wpgp' ); ?></h2>
				<p><input type="text" name="wpgp_post_title" placeholder="<?php esc_html_e( 'Post Title', 'wpgp' ); ?>" /></p>
				<p>
					<?php
						// Get the list of registered custom post types.
						$args       = array(
							'public'   => true,
							'_builtin' => false,
						);
						$output     = 'objects';
						$operator   = 'and';
						$post_types = get_post_types( $args, $output, $operator );
						?>
					<select name="wpgp_post_type">
						<option value=""><?php esc_html_e( 'Select a post type', 'wpgp' ); ?></option>
						<?php
						foreach ( $post_types as $name => $post_type ) {
							printf( '<option value="%s">%s</option>', esc_attr( $name ), esc_attr( $post_type->label ) );
						}
						?>
					</select>
				</p>

				<div class="wpgp-editor">
					<?php
						$content  = '';
						$settings = array(
							'theme_advanced_buttons1' => 'bold, italic, ul, pH, pH_min',
							'media_buttons'           => false,
							'textarea_rows'           => 8,
							'tabindex'                => 4,
							'teeny'                   => true,
							'tinymce'                 => array(
								'init_instance_callback' => 'wpgp_editor_callback',
							),
						);
						wp_editor( $content, 'wpgp_post_content', $settings );
						?>
				</div>

				<p><textarea name="wpgp_post_excerpt" placeholder="<?php esc_html_e( 'Write the post excerpt here.', 'wpgp' ); ?>"></textarea></p>
				<p>
					<input type="file" name="wpgp_post_thumbnail" /> <br/>
					<small class="wpgp-info"><?php esc_html_e( 'Only jpg, png and gif files are allowed.', 'wpgp' ); ?></small>
				</p>
				<input type="hidden" name="action" value="submit_post">
				<?php wp_nonce_field( 'wpgp_nonce_action', 'wpgp_nonce_field' ); ?>
				<p><input type="submit" name="wpgp_submit" value="submit" class="wpgp-submit" /></p>
				<div class="wpgp-response"><p></p></div>
				<div class="wpgp-loader"></div>
			</form>
		</div>
			<?php
			// Add the necessary scripts for form shortcode.
			wp_enqueue_script( 'wpgp-validate-script' );
			wp_enqueue_script( 'wpgp-additional-method-script' );
			wp_enqueue_script( 'wpgp-form-script' );

		}
		return ob_get_clean();
	}

	/**
	 * Adds the shortcode scripts and styles
	 */
	public function shortcode_scripts() {
		global $post;

		wp_enqueue_style( 'wpgp-form-style', WPGP_URL . 'assets/css/wpgp-form-style.css', array(), WPGP_VERSION );
		wp_register_script( 'wpgp-validate-script', WPGP_URL . 'assets/js/jquery.validate.min.js', array( 'jquery' ), WPGP_VERSION, true );
		wp_register_script( 'wpgp-additional-method-script', WPGP_URL . 'assets/js/additional-methods.min.js', array( 'jquery' ), WPGP_VERSION, true );
		wp_register_script( 'wpgp-form-script', WPGP_URL . 'assets/js/wpgp-form-script.js', array( 'jquery', 'wpgp-validate-script', 'wpgp-additional-method-script' ), WPGP_VERSION, true );

		wp_localize_script(
			'wpgp-form-script',
			'params',
			array(
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'messages' => array(
					'post_title'     => array(
						'required' => __( 'Post title is required.', 'wpgp' ),
					),
					'post_type'      => array(
						'required' => __( 'Post type is required.', 'wpgp' ),
					),
					'post_content'   => array(
						'required' => __( 'Post content is required.', 'wpgp' ),
					),
					'post_excerpt'   => array(
						'required' => __( 'Post excerpt is required.', 'wpgp' ),
					),
					'post_thumbnail' => array(
						'required' => __( 'Post thumbnail is required.', 'wpgp' ),
						'required' => __( 'Please select a valid file type.', 'wpgp' ),
					),
				),
			)
		);
	}

	/**
	 * Handles the AJAX request done through form
	 */
	public function submit_post() {
		$nonce = filter_input( INPUT_POST, 'wpgp_nonce_field', FILTER_SANITIZE_STRING );
		// Validate the ajax request nonce.
		if ( ! wp_verify_nonce( $nonce, 'wpgp_nonce_action' ) ) {
			$msg = __( 'Sorry, you are not allowed to perform this action.', 'wpgp' );
			wp_send_json_error( array( 'msg' => $msg ) );
		}

		// $_POST data
		$post_title   = filter_input( INPUT_POST, 'wpgp_post_title', FILTER_DEFAULT );
		$post_type    = filter_input( INPUT_POST, 'wpgp_post_type', FILTER_DEFAULT );
		$post_content = filter_input( INPUT_POST, 'wpgp_post_content', FILTER_DEFAULT );
		$post_excerpt = filter_input( INPUT_POST, 'wpgp_post_excerpt', FILTER_DEFAULT );

		$filters        = array(
			'name'     => FILTER_DEFAULT,
			'type'     => FILTER_DEFAULT,
			'tmp_name' => FILTER_DEFAULT,
			'error'    => FILTER_VALIDATE_INT,
			'size'     => FILTER_VALIDATE_INT,
		);
		$post_thumbnail = ! empty( $_FILES['wpgp_post_thumbnail'] ) ? wp_unslash( $_FILES['wpgp_post_thumbnail'] ) : array();
		$post_thumbnail = filter_var_array( $post_thumbnail, $filters );

		$errors = array();

		// Trim to check whether they are blank.
		$post_title   = trim( $post_title );
		$post_type    = trim( $post_type );
		$post_content = trim( $post_content );
		$post_excerpt = trim( $post_excerpt );

		if ( empty( $post_title ) ) {
			$errors[] = __( 'Post title is required.', 'wpgp' );
		}

		if ( empty( $post_type ) ) {
			$errors[] = __( 'Post type is required.', 'wpgp' );
		}

		if ( empty( $post_content ) ) {
			$errors[] = __( 'Post content is required.', 'wpgp' );
		}

		if ( empty( $post_excerpt ) ) {
			$errors[] = __( 'Post excerpt is required.', 'wpgp' );
		}

		if ( empty( $post_thumbnail['name'] ) ) {
			$errors[] = __( 'Post thumbnail is required.', 'wpgp' );
		}

		if ( ! empty( $errors ) ) {
			$errors = implode( '<br/>', $errors );
			wp_send_json_error( array( 'msg' => $errors ) );
		} else {

			// Insert Post.
			$post_data_to_insert = array(
				'post_title'   => wp_strip_all_tags( $post_title ),
				'post_type'    => $post_type,
				'post_status'  => 'pending',
				'post_content' => stripslashes( wp_kses_post( $post_content ) ),
				'post_excerpt' => $post_excerpt,
				'post_author'  => get_current_user_id(),
			);

			$post_id = wp_insert_post( $post_data_to_insert, true );

			// If error while inserting the post.
			if ( is_wp_error( $post_id ) ) {
				wp_send_json_error( array( 'msg' => $post_id->get_error_message() ) );
			} else {

				// This is required for wp_handle_upload to handle the file upload.
				if ( ! function_exists( 'wp_handle_upload' ) ) {
					require_once ABSPATH . 'wp-admin/includes/file.php';
				}

				$uploadedfile     = $post_thumbnail;
				$upload_overrides = array(
					'test_form' => false,
				);
				$movefile         = wp_handle_upload( $uploadedfile, $upload_overrides );

				// Check if file uploaded successfully.
				if ( $movefile && ! isset( $movefile['error'] ) ) {

					$wp_upload_dir = wp_upload_dir();

					$attachment = array(
						'guid'           => $wp_upload_dir['url'] . '/' . basename( $movefile['file'] ),
						'post_mime_type' => $movefile['type'],
						'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $movefile['file'] ) ),
						'post_content'   => '',
						'post_status'    => 'inherit',
						'post_parent'    => $post_id,
					);
					$attach_id  = wp_insert_attachment( $attachment, $movefile['file'] );

					require_once ABSPATH . 'wp-admin/includes/image.php';

					$attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
					wp_update_attachment_metadata( $attach_id, $attach_data );

					// Assign the file as the featured image.
					set_post_thumbnail( $post_id, $attach_id );

					$msg = __( 'Post submitted successfully.', 'wpgp' );
				} else {
					// translators: %s represents the error.
					$msg = sprintf( __( 'Post submitted successfully but the file was not uploaded due to some error : %s', 'wpgp' ), $movefile['error'] );
				}

				// Email to admin.
				$admin_email = get_option( 'admin_email' );

				// From user.
				$user_info  = get_userdata( get_current_user_id() );
				$user_name  = $user_info->display_name;
				$user_email = $user_info->user_email;

				// translators: %s represents the username.
				$email_subject = sprintf( __( 'New post has been published by %s', 'wpgp' ), $user_name );

				// Email Headers.
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$headers[] = 'From: ' . $user_name . ' <' . $user_email . '>';

				$email_body  = '<html><body>';
				$email_body .= __( 'Hello Admin,', 'wpgp' ) . '<br />';
				// translators: %s represents the username.
				$email_body .= sprintf( __( 'New post has been published by %s', 'wpgp' ), $user_name ) . '<br />';

				// Get the edit post link from custom function.
				$post_edit_link = $this->wpgp_get_edit_post_link( $post_id );
				// translators: %s represents the post edit link.
				$email_body .= sprintf( __( 'Please take your time and review it : %s', 'wpgp' ), $post_edit_link ) . '<br />';
				$email_body .= __( 'Thanks', 'wpgp' );
				$email_body .= '</body></html>';

				$sent = wp_mail( $admin_email, $email_subject, $email_body, $headers );

				if ( $sent ) {
					// translators: %s represents the message.
					$msg = sprintf( __( '%s Admin will review and get back to you.', 'wpgp' ), $msg );
				} else {
					// translators: %s represents the message.
					$msg = sprintf( __( '%s Error while sending email to admin.', 'wpgp' ), $msg );
				}

				wp_send_json_success( array( 'msg' => $msg ) );
			}
		}
	}

	/**
	 * Function to handle shortcode for pending posts
	 *
	 * @param array $atts contains array of shortcode attributes.
	 *
	 * @return  string HTML of the list of pending posts or blank.
	 */
	public function pending_posts_shortcode_handler( $atts ) {
		ob_start();

		$user  = wp_get_current_user();
		$roles = (array) $user->roles;

		if ( is_user_logged_in() && ( current_user_can( 'edit_posts' ) && in_array( 'author', $roles, true ) ) || current_user_can( 'manage_options' ) ) {

			$atts = shortcode_atts(
				array(
					'post_type' => 'any',
				),
				$atts
			);

			$default_posts_per_page = get_option( 'posts_per_page' );
			$paged                  = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			$args                   = array(
				'post_status'    => 'pending',
				'post_type'      => $atts['post_type'],
				'posts_per_page' => $default_posts_per_page,
				'author'         => get_current_user_id(),
				'paged'          => $paged,
			);

			$the_query = new WP_Query( $args );
			?>
			<div class="wpgp-posts-wrapper">
				<?php
				if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						?>
						<div class="wpgp-pending-post">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail();
							}
							?>
							<h2><?php the_title(); ?></h2>
							<p><?php echo get_the_excerpt(); ?></p>
						</div>
						<?php
					}
					?>
					<div class="wpgp-pagination">
						<?php
							$pagination_links = paginate_links(
								array(
									'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
									'total'     => $the_query->max_num_pages,
									'current'   => max( 1, get_query_var( 'paged' ) ),
									'format'    => '?paged=%#%',
									'show_all'  => false,
									'type'      => 'plain',
									'end_size'  => 2,
									'mid_size'  => 1,
									'prev_next' => true,
									'prev_text' => sprintf( '<i></i> %1$s', __( 'Newer Posts', 'wpgp' ) ),
									'next_text' => sprintf( '%1$s <i></i>', __( 'Older Posts', 'wpgp' ) ),
								)
							);

							// Output escaped pagination links.
							echo wp_kses_post( $pagination_links );
						?>
					</div>
					<?php
					wp_reset_postdata();
				} else {
					?>
					<p><?php esc_html_e( 'No posts found.', 'wpgp' ); ?></p>
					<?php
				}
				?>
			</div>	
			<?php

		}
		return ob_get_clean();
	}

	/**
	 * Generates and returns the edit post link for passed post id
	 *
	 * @param  int $post_id post edit link.
	 *
	 * @return string
	 */
	public function wpgp_get_edit_post_link( $post_id ) {

		if ( empty( $post_id ) && current_user_can( 'edit_posts' ) ) {
			return '';
		}

		$query_strings = sprintf( 'post=%d&action=edit', $post_id );
		$url           = admin_url( sprintf( 'post.php?%s', $query_strings ) );

		return $url;
	}

}
