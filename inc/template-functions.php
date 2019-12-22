<?php
require "classes/indigo_Walker_Comment.php";

//Show Profile
function wp_indigo_show_profile() {
	get_template_part( "template-parts/profile" );
}

// Menu Generator
function wp_indigo_show_menu() {
	if ( has_nav_menu( 'primary-menu' ) ) {
		$wp_indigo_menu_args = array(
			'theme_location'  => 'primary-menu',
			'menu_class'      => 'list',
			'container'       => 'div',
			'container_class' => 'nav-home',
			'depth'           => 1
		);
		if ( ! is_front_page() ) {
			$wp_indigo_menu_args['container_class'] = 'nav';
			$wp_indigo_menu_args['depth']           = 0;
		}
		wp_nav_menu( $wp_indigo_menu_args );
	}
}

// Show Post Tags
function wp_indigo_show_tags() {
	the_tags( '', ' ', '' );
}

// Show Name Field
function wp_indigo_show_avatar() {
	if ( has_custom_logo() ) {
		the_custom_logo();
	}
}

/**
 * Show socials list
 *
 * @param $wp_indigo_social_names | array
 */
function wp_indigo_show_socials( $wp_indigo_social_names ) {
	foreach ( $wp_indigo_social_names as $wp_indigo_social_name ) {
		$social = get_theme_mod( $wp_indigo_social_name );
		if ( $social != "" ) {
			$name = explode( '-', $wp_indigo_social_name );
			if ( strpos( $name[1], 'mail' ) !== false ) {
				$output = '<a rel="noopener" aria-label="Email me" class="link" data-title="' . $social . '" href="mailto:' . $social . '" target="_blank">
			<svg class="icon icon-facebook"><use xlink:href="' . get_template_directory_uri() . '/assets/images/defs.svg#icon-' . $name[1] . '"></use></svg>
		</a>';
			} else {
				$name = explode( '-', $wp_indigo_social_name );
				$output = '<a rel="noopener" aria-label="View ' . $name[1] . ' page" class="link" data-title="' . $social . '" href="' . $social . '" target="_blank">
			<svg class="icon icon-facebook"><use xlink:href="' . get_template_directory_uri() . '/assets/images/defs.svg#icon-' . $name[1] . '"></use></svg>
		</a>';
			}

			echo esc_html($output);
		}
	}
}

/**
 * Check active socials
 *
 * @param $wp_indigo_social_names
 *
 * @return bool
 */
function wp_indigo_check_socials( $wp_indigo_social_names ) {
	foreach ( $wp_indigo_social_names as $wp_indigo_social_name ) {
		$social = get_theme_mod( $wp_indigo_social_name );
		if ( $social != "" ) {
			return true;
		}
	}

	return false;
}

// Load theme typography
function wp_indigo_typography() {
	$wp_indigo_text_typography            = get_theme_mod( 'text_typography' );
	$wp_indigo_heading_typography         = get_theme_mod( 'headings_typography' );
	$wp_indigo_default_heading_typography = array(
		'font-family' => "Roboto Mono",
		'font-size'   => "26px",
		'variant'     => 'regular',
		'line-height' => '31px',
		'color'       => '#1a1a1a'
	);
	$default_text_typography    = array(
		'font-family' => "Roboto Mono",
		'font-size'   => "16px",
		'variant'     => 'regular',
		'line-height' => '28px',
		'color'       => '#666666'
	);
	if ( empty( $wp_indigo_heading_typography ) ) {
		$wp_indigo_heading_typography = $wp_indigo_default_heading_typography;
	} else {
		$wp_indigo_heading_typography = array_merge( $wp_indigo_default_heading_typography, $wp_indigo_heading_typography );
	}
	if ( empty( $wp_indigo_text_typography ) ) {
		$wp_indigo_text_typography = $default_text_typography;
	} else {
		$wp_indigo_text_typography = array_merge( $default_text_typography, $wp_indigo_text_typography );
	}
	$html = '<style>
	        :root {
				--heading-typography-font-size: ' . $wp_indigo_heading_typography["font-size"] . ';
	            --heading-typography-font-family: ' . $wp_indigo_heading_typography["font-family"] . ';
	            --heading-typography-line-height: ' . $wp_indigo_heading_typography["line-height"] . ';
	            --heading-typography-variant: ' . $wp_indigo_heading_typography["variant"] . ';
	            --text-typography-font-size: ' . $wp_indigo_text_typography["font-size"] . ';
	            --text-typography-font-family: ' . $wp_indigo_text_typography["font-family"] . ';
	            --text-typography-line-height: ' . $wp_indigo_text_typography["line-height"] . ';
	            --text-typography-variant: ' . $wp_indigo_text_typography["variant"] . ';
	
	            --primary-color: ' . get_theme_mod( "branding_primary_color", "#3F51B5" ) . ';
	            --secondary-color: ' . $wp_indigo_heading_typography["color"] . ';
	            --tertiary-color: ' . $wp_indigo_text_typography['color'] . ';
			}
		    </style>';
	echo $html;
}

//
function wp_indigo_get_discussion_data() {
	static $discussion, $post_id;
	$wp_indigo_current_post_id = get_the_ID();
	if ( $wp_indigo_current_post_id === $post_id ) {
		return $discussion; /* If we have discussion information for post ID, return cached object */
	} else {
		$post_id = $wp_indigo_current_post_id;
	}
	$wp_indigo_comments = get_comments(
		array(
			'post_id' => $wp_indigo_current_post_id,
			'orderby' => 'comment_date_gmt',
			'order'   => get_option( 'comment_order', 'asc' ), /* Respect comment order from Settings » Discussion. */
			'status'  => 'approve',
			'number'  => 20, /* Only retrieve the last 20 comments, as the end goal is just 6 unique authors */
		)
	);
	$wp_indigo_authors  = array();
	foreach ( $wp_indigo_comments as $wp_indigo_comment ) {
		$wp_indigo_authors[] = ( (int) $wp_indigo_comment->user_id > 0 ) ? (int) $wp_indigo_comment->user_id : $wp_indigo_comment->comment_author_email;
	}
	$wp_indigo_authors    = array_unique( $wp_indigo_authors );
	$discussion = (object) array(
		'authors'   => array_slice( $wp_indigo_authors, 0, 6 ),           /* Six unique authors commenting on the post. */
		'responses' => get_comments_number( $wp_indigo_current_post_id ), /* Number of responses. */
	);

	return $discussion;
}

//
function wp_indigo_comment_form( $wp_indigo_order ) {
	if ( true === $wp_indigo_order || strtolower( $wp_indigo_order ) === strtolower( get_option( 'comment_order', 'asc' ) ) ) {
		$wp_indigo_fields = array(
			'author'  =>
				'<p class="comment-form-author">' .
				'<input placeholder="Your Name" id="author" name="author" type="text" size="30" /></p>',
			'email'   =>
				'<p class="comment-form-email">' .
				'<input placeholder="Your Email" id="email" name="email" type="email" value="" size="30" /></p>',
			'url'     => '',
			'cookies' => ''
		);
		comment_form(
			array(
				'logged_in_as'         => null,
				'title_reply'          => null,
				'comment_notes_before' => false,
				'label_submit'         => 'Submit',
				'fields'               => $wp_indigo_fields,
				'comment_field'        => '<p class="comment-form-comment"><textarea placeholder="Write your comment" id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>'
			)
		);
	}
}