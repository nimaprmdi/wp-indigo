<?php /*  Template Name: Blog */
/*
 * Blog page template
 *
 * You can just set Blog page in Reading setting to a page and everything done. your blog articles will show on.
 *
 * */
get_header(); ?>
<?php wp_indigo_show_profile(); ?>
    <section class="blog">
        <div class="list">
			<?php
			$args = array(
				'post_status'    => 'publish',
				'posts_per_page' => get_option( 'posts_per_page' ),
				'paged'          => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
			);

			$wp_indigo_posts = new WP_Query( $args );

			if ( $wp_indigo_posts->have_posts() ) {

				while ( $wp_indigo_posts->have_posts() ) {

					$wp_indigo_posts->the_post();

					get_template_part( 'template-parts/list', 'blog' );

				} ?>
                <div class="pagination">
                    <div class="nav-links">
						<?php
						$paginate_args = array(
							'base'               => '%_%',
							'format'             => '?paged=%#%',
							'total'              => $wp_indigo_posts->max_num_pages,
							'current'            => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
							'show_all'           => false,
							'end_size'           => 1,
							'mid_size'           => 2,
							'prev_next'          => true,
							'prev_text'          => __( 'Previous', 'wp-indigo' ),
							'next_text'          => __( 'Next', 'wp-indigo' ),
							'type'               => 'plain',
							'add_args'           => false,
							'add_fragment'       => '',
							'before_page_number' => '',
							'after_page_number'  => ''
						);
						echo paginate_links( $paginate_args );
						?>
                    </div>
                </div>
				<?php

			} else {
				get_template_part( 'template-parts/content', 'none' );
			}

			wp_reset_postdata();
			?>
        </div>
    </section>
<?php get_footer();