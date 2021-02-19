<?php 
	$url = get_post_meta(get_post()->ID, '_frachop_gphoto_featured', true);
	if ($url)
	{ 
		$container_width = hu_get_option('container-width');
		if (! $container_width )
			$container_width = 1380;

		$w = intval($container_width * 0.75);
		$h = intval($w * 410 / 980);
		?>
		<div class="frachop-post-heading-featured">
			<img src="<?php echo "$url=w{$w}-h{$h}-c" ?>" alt="" title="" width="100%"/>
		</div>
		<?php
	}
?>
<h1 class="post-title entry-title"><?php the_title(); ?></h1>
<?php get_template_part('parts/single-author-date'); ?>