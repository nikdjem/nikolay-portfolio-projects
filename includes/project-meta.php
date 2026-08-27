<?php
/**
 * Project metadata registration and admin UI.
 *
 * @package Nikolay_Portfolio_Projects
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed project status machine values.
 *
 * @return string[]
 */
function np_projects_get_status_values() {
	return array( 'live', 'stable', 'in-progress' );
}

/**
 * REST schema enum values for project status (includes empty/unset).
 *
 * @return string[]
 */
function np_projects_get_status_rest_enum() {
	return array_merge( array( '' ), np_projects_get_status_values() );
}

/**
 * Human-readable labels for project status values.
 *
 * @return array<string, string>
 */
function np_projects_get_status_labels() {
	return array(
		'live'        => __( 'LIVE', 'nikolay-portfolio-projects' ),
		'stable'      => __( 'STABLE', 'nikolay-portfolio-projects' ),
		'in-progress' => __( 'IN PROGRESS', 'nikolay-portfolio-projects' ),
	);
}

/**
 * Determine whether the current user may edit project metadata.
 *
 * @param bool   $allowed  Whether the user can add the meta.
 * @param string $meta_key Meta key.
 * @param int    $post_id  Post ID.
 * @return bool
 */
function np_projects_meta_auth_callback( $allowed, $meta_key, $post_id ) {
	unset( $meta_key );

	if ( ! $post_id ) {
		return true;
	}

	if ( 'project' !== get_post_type( $post_id ) ) {
		return false;
	}

	return current_user_can( 'edit_post', $post_id );
}

/**
 * Sanitize project status meta.
 *
 * @param mixed $value Raw meta value.
 * @return string
 */
function np_projects_sanitize_status_meta( $value ) {
	if ( ! is_string( $value ) ) {
		return '';
	}

	$value = sanitize_key( str_replace( '_', '-', $value ) );

	if ( '' === $value ) {
		return '';
	}

	if ( ! in_array( $value, np_projects_get_status_values(), true ) ) {
		return '';
	}

	return $value;
}

/**
 * Sanitize optional HTTP/HTTPS URL meta.
 *
 * @param mixed $value Raw meta value.
 * @return string
 */
function np_projects_sanitize_url_meta( $value ) {
	if ( ! is_string( $value ) ) {
		return '';
	}

	$value = trim( $value );

	if ( '' === $value ) {
		return '';
	}

	$sanitized = esc_url_raw( $value );

	if ( '' === $sanitized ) {
		return '';
	}

	$parsed = wp_parse_url( $sanitized );

	if ( empty( $parsed['scheme'] ) || ! in_array( $parsed['scheme'], array( 'http', 'https' ), true ) ) {
		return '';
	}

	return $sanitized;
}

/**
 * Register project metadata fields.
 *
 * @return void
 */
function np_projects_register_meta() {
	register_post_meta(
		'project',
		'_np_project_status',
		array(
			'type'              => 'string',
			'single'            => true,
			'default'           => '',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
					'enum' => np_projects_get_status_rest_enum(),
				),
			),
			'sanitize_callback' => 'np_projects_sanitize_status_meta',
			'auth_callback'     => 'np_projects_meta_auth_callback',
		)
	);

	register_post_meta(
		'project',
		'_np_project_github_url',
		array(
			'type'              => 'string',
			'single'            => true,
			'default'           => '',
			'show_in_rest'      => true,
			'sanitize_callback' => 'np_projects_sanitize_url_meta',
			'auth_callback'     => 'np_projects_meta_auth_callback',
		)
	);

	register_post_meta(
		'project',
		'_np_project_live_url',
		array(
			'type'              => 'string',
			'single'            => true,
			'default'           => '',
			'show_in_rest'      => true,
			'sanitize_callback' => 'np_projects_sanitize_url_meta',
			'auth_callback'     => 'np_projects_meta_auth_callback',
		)
	);
}
add_action( 'init', 'np_projects_register_meta', 20 );

/**
 * Register the project metadata meta box.
 *
 * @return void
 */
function np_projects_register_meta_box() {
	add_meta_box(
		'np-project-metadata',
		__( 'Project Metadata', 'nikolay-portfolio-projects' ),
		'np_projects_render_meta_box',
		'project',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'np_projects_register_meta_box' );

/**
 * Render project metadata fields in the editor.
 *
 * @param WP_Post $post Current post.
 * @return void
 */
function np_projects_render_meta_box( $post ) {
	wp_nonce_field( 'np_projects_save_meta', 'np_projects_meta_nonce' );

	$status     = get_post_meta( $post->ID, '_np_project_status', true );
	$github_url = get_post_meta( $post->ID, '_np_project_github_url', true );
	$live_url   = get_post_meta( $post->ID, '_np_project_live_url', true );

	if ( ! is_string( $status ) ) {
		$status = '';
	}

	if ( ! is_string( $github_url ) ) {
		$github_url = '';
	}

	if ( ! is_string( $live_url ) ) {
		$live_url = '';
	}
	?>
	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row">
					<label for="np-project-status"><?php esc_html_e( 'Project Status', 'nikolay-portfolio-projects' ); ?></label>
				</th>
				<td>
					<select name="np_project_status" id="np-project-status">
						<option value="" <?php selected( $status, '' ); ?>><?php esc_html_e( '— Select —', 'nikolay-portfolio-projects' ); ?></option>
						<?php foreach ( np_projects_get_status_labels() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $status, $value ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php esc_html_e( 'Live URLs may only be shown publicly when status is LIVE.', 'nikolay-portfolio-projects' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="np-project-github-url"><?php esc_html_e( 'GitHub Repository URL', 'nikolay-portfolio-projects' ); ?></label>
				</th>
				<td>
					<input type="url" class="large-text code" name="np_project_github_url" id="np-project-github-url" value="<?php echo esc_attr( $github_url ); ?>" placeholder="https://github.com/" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="np-project-live-url"><?php esc_html_e( 'Live Project URL', 'nikolay-portfolio-projects' ); ?></label>
				</th>
				<td>
					<input type="url" class="large-text code" name="np_project_live_url" id="np-project-live-url" value="<?php echo esc_attr( $live_url ); ?>" placeholder="https://example.com/" />
					<p class="description"><?php esc_html_e( 'Used only when Project Status is LIVE.', 'nikolay-portfolio-projects' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Persist project metadata from the editor meta box.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function np_projects_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['np_projects_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['np_projects_meta_nonce'] ) ), 'np_projects_save_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( 'project' !== get_post_type( $post_id ) ) {
		return;
	}

	$status = isset( $_POST['np_project_status'] ) ? np_projects_sanitize_status_meta( wp_unslash( $_POST['np_project_status'] ) ) : '';
	update_post_meta( $post_id, '_np_project_status', $status );

	$github_url = isset( $_POST['np_project_github_url'] ) ? np_projects_sanitize_url_meta( wp_unslash( $_POST['np_project_github_url'] ) ) : '';
	update_post_meta( $post_id, '_np_project_github_url', $github_url );

	$live_url = isset( $_POST['np_project_live_url'] ) ? np_projects_sanitize_url_meta( wp_unslash( $_POST['np_project_live_url'] ) ) : '';
	update_post_meta( $post_id, '_np_project_live_url', $live_url );
}
add_action( 'save_post_project', 'np_projects_save_meta_box' );

/**
 * Get the display label for a stored project status value.
 *
 * @param string $status Stored status machine value.
 * @return string
 */
function np_projects_get_status_label( $status ) {
	if ( ! is_string( $status ) || '' === $status ) {
		return '';
	}

	$labels = np_projects_get_status_labels();

	return $labels[ $status ] ?? '';
}

/**
 * Determine whether a Live Project CTA may be rendered for a project.
 *
 * Claim safety: Live URLs render only when status is live and URL is set.
 *
 * @param int $post_id Project post ID.
 * @return bool
 */
function np_projects_should_render_live_link( $post_id ) {
	$post_id = (int) $post_id;

	if ( $post_id <= 0 || 'project' !== get_post_type( $post_id ) ) {
		return false;
	}

	$status   = get_post_meta( $post_id, '_np_project_status', true );
	$live_url = get_post_meta( $post_id, '_np_project_live_url', true );

	return 'live' === $status && is_string( $live_url ) && '' !== $live_url;
}

/**
 * Determine whether a GitHub CTA may be rendered for a project.
 *
 * @param int $post_id Project post ID.
 * @return bool
 */
function np_projects_should_render_github_link( $post_id ) {
	$post_id = (int) $post_id;

	if ( $post_id <= 0 || 'project' !== get_post_type( $post_id ) ) {
		return false;
	}

	$github_url = get_post_meta( $post_id, '_np_project_github_url', true );

	return is_string( $github_url ) && '' !== $github_url;
}
