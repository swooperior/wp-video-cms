<?php
get_header();
?>

<main id="site-content" role="main">
	<style>
		#video-container{
			width:80%;
			height:auto;
			margin:1vh auto;
		}
	</style>
	<?php

	if ( have_posts() ) {

		while ( have_posts() ) {

			the_post();
			echo ("
				<div id='video-container'>
				".do_shortcode('[rm_video]')."
				</div>		  
			");
            get_template_part( 'template-parts/content', get_post_type() );
		}
	}

	?>

</main><!-- #site-content -->

/<?php// get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php get_footer(); ?>

