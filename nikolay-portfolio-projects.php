<?php
/**
 * Plugin Name: Nikolay Portfolio Projects
 * Description: Project content model for the Nikolay Portfolio.
 * Version: 1.0.0
 * Author: Nikolay
 * Text Domain: nikolay-portfolio-projects
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package Nikolay_Portfolio_Projects
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bootstrap CPT, taxonomy, and default terms.
 *
 * @return void
 */
function np_projects_init() {
	np_projects_register_post_type();
	np_projects_register_taxonomy();
	np_projects_register_default_terms();
}
add_action( 'init', 'np_projects_init' );

/**
 * Register the project custom post type.
 *
 * @return void
 */
function np_projects_register_post_type() {
	$labels = array(
		'name'               => __( 'Projects', 'nikolay-portfolio-projects' ),
		'singular_name'      => __( 'Project', 'nikolay-portfolio-projects' ),
		'add_new'            => __( 'Add New', 'nikolay-portfolio-projects' ),
		'add_new_item'       => __( 'Add New Project', 'nikolay-portfolio-projects' ),
		'edit_item'          => __( 'Edit Project', 'nikolay-portfolio-projects' ),
		'new_item'           => __( 'New Project', 'nikolay-portfolio-projects' ),
		'view_item'          => __( 'View Project', 'nikolay-portfolio-projects' ),
		'search_items'       => __( 'Search Projects', 'nikolay-portfolio-projects' ),
		'not_found'          => __( 'No Projects Found', 'nikolay-portfolio-projects' ),
		'not_found_in_trash' => __( 'No Projects Found in Trash', 'nikolay-portfolio-projects' ),
		'menu_name'          => __( 'Projects', 'nikolay-portfolio-projects' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'show_ui'            => true,
		'show_in_rest'       => true,
		'has_archive'        => false,
		'hierarchical'       => false,
		'exclude_from_search' => false,
		'publicly_queryable' => true,
		'rewrite'            => array(
			'slug' => 'work',
		),
		'supports'           => array(
			'title',
			'editor',
			'excerpt',
			'thumbnail',
			'revisions',
			'page-attributes',
		),
		'menu_icon'          => 'dashicons-portfolio',
	);

	register_post_type( 'project', $args );
}

/**
 * Register the project category taxonomy.
 *
 * @return void
 */
function np_projects_register_taxonomy() {
	$labels = array(
		'name'          => __( 'Project Categories', 'nikolay-portfolio-projects' ),
		'singular_name' => __( 'Project Category', 'nikolay-portfolio-projects' ),
		'search_items'  => __( 'Search Project Categories', 'nikolay-portfolio-projects' ),
		'all_items'     => __( 'All Project Categories', 'nikolay-portfolio-projects' ),
		'edit_item'     => __( 'Edit Project Category', 'nikolay-portfolio-projects' ),
		'update_item'   => __( 'Update Project Category', 'nikolay-portfolio-projects' ),
		'add_new_item'  => __( 'Add New Project Category', 'nikolay-portfolio-projects' ),
		'new_item_name' => __( 'New Project Category', 'nikolay-portfolio-projects' ),
		'menu_name'     => __( 'Project Categories', 'nikolay-portfolio-projects' ),
	);

	$args = array(
		'labels'            => $labels,
		'public'            => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'hierarchical'      => false,
		'show_admin_column' => true,
		'rewrite'           => array(
			'slug' => 'project-category',
		),
	);

	register_taxonomy( 'project_category', array( 'project' ), $args );
}

/**
 * Insert default project categories when they do not already exist.
 *
 * @return void
 */
function np_projects_register_default_terms() {
	if ( ! taxonomy_exists( 'project_category' ) ) {
		return;
	}

	$terms = array(
		'intelligence' => __( 'Intelligence', 'nikolay-portfolio-projects' ),
		'commerce'     => __( 'Commerce', 'nikolay-portfolio-projects' ),
		'interface'    => __( 'Interface', 'nikolay-portfolio-projects' ),
	);

	foreach ( $terms as $slug => $name ) {
		if ( term_exists( $slug, 'project_category' ) ) {
			continue;
		}

		wp_insert_term(
			$name,
			'project_category',
			array(
				'slug' => $slug,
			)
		);
	}
}

/**
 * Flush rewrite rules after registration on activation.
 *
 * @return void
 */
function np_projects_activate() {
	np_projects_register_post_type();
	np_projects_register_taxonomy();
	np_projects_register_default_terms();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'np_projects_activate' );

/**
 * Flush rewrite rules on deactivation without deleting content.
 *
 * @return void
 */
function np_projects_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'np_projects_deactivate' );
