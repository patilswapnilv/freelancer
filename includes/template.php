<?php

/**
 * Template functions.
 *
 * @package    	Freelancer/Includes/Frontend
 * @since      	0.2.0
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @link       	http://nunoapps.com/plugins/freelancer
 * @license  	GPL-2.0+
 */

/**
 * Display Author box on front-end.
 *
 * @since 	0.2.3
 * @access 	public
 * @return 	void
 *
 * @todo 	Update with defined user meta.
 */
function freelancer_author_box() { ?>
	<div class="author-profile vcard">
		<?php echo get_avatar( get_the_author_meta( 'user_email' ), '96' ); ?>

		<h4 class="author-name fn n"><?php echo sprintf( __( 'Article written by %s', 'freelancer' ), get_author_posts_link() ); ?></h4>

		<p class="author-description author-bio">
			<?php the_author_meta( 'description' ); ?>
		</p>

		<?php if ( get_the_author_meta( 'twitter' ) ) { ?>
			<p class="twitter clear">
				<a href="http://twitter.com/<?php the_author_meta( 'twitter' ); ?>" title="Follow <?php the_author_meta( 'display_name' ); ?> on Twitter">Follow <?php the_author_meta( 'display_name' ); ?> on Twitter</a>
			</p>
		<?php } // End check for twitter ?>
	</div><?php
}