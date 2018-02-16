<?php

/**
 * Class RBM_CPT_Programs
 *
 * Creates the post type.
 *
 * @since 1.0.0
 */
class RBM_CPT_Programs extends RBM_CPT {

	public $post_type = 'programs';
	public $label_singular = null;
	public $label_plural = null;
	public $labels = array();
	public $icon = 'welcome-write-blog';
	public $post_args = array(
		'hierarchical' => false,
		'supports'     => array( 'title', 'editor', 'author', 'thumbnail' ),
		'has_archive'  => true,
		'rewrite'      => array(
			'slug'       => 'program',
			'with_front' => false,
			'feeds'      => false,
			'pages'      => true
		),
		'capability_type' => 'post',
	);

	/**
	 * RBM_CPT_Programs constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		// This allows us to Localize the Labels
		$this->label_singular = __( 'Program', 'cpt-programs' );
		$this->label_plural   = __( 'Programs', 'cpt-programs' );

		$this->labels = array(
			'menu_name' => __( 'Programs', 'cpt-programs' ),
			'all_items' => __( 'All Programs', 'cpt-programs' ),
		);

		parent::__construct();
		
	}
	
}