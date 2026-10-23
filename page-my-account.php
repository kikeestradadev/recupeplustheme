<?php
/**
 * Template for the page with slug `my-account`.
 * Orders, addresses, and account details are endpoints of this page.
 *
 * @package wpboilerplate
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article class="account-page" id="post-<?php the_ID(); ?>">
		<div class="main-container">
			<div class="container">
				<header class="account-page__header">
					<h1 class="account-page__title"><?php the_title(); ?></h1>
				</header>
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
