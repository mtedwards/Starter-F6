<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package starter 2
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<?php if ( have_posts() ) { ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) { the_post(); ?>

				<?php
					the_content();
				?>

			<?php } // endwhile; ?>

			<?php the_posts_navigation(); ?>

		<?php } else { ?>

      			<p>Oops. There is nothing here.</p>

		<?php } //endif; ?>

		</main><!-- #main -->
		<?php // get_sidebar(); ?>
	</section><!-- #primary -->


<?php get_footer(); ?>
