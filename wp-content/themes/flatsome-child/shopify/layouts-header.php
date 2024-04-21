<!DOCTYPE html>
<!--[if IE 9 ]> <html <?php language_attributes(); ?> class="ie9 <?php flatsome_html_classes(); ?>"> <![endif]-->
<!--[if IE 8 ]> <html <?php language_attributes(); ?> class="ie8 <?php flatsome_html_classes(); ?>"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php language_attributes(); ?> class="<?php flatsome_html_classes(); ?>"> <!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php wp_head(); ?>
	<link rel="stylesheet" href="<?=get_stylesheet_directory_uri().'/shopify/assets/main.css'?>">
</head>

<?php 
$site_logo_id        = flatsome_option( 'site_logo' );
$site_logo           = wp_get_attachment_image_src( $site_logo_id, 'large' );

?>
<body <?php body_class(); ?>>
<div id="wrapper">
	<header id="header" class="header _header-shopify">
		<div class="_header-shopify-wrapper _flex-center-y">
				<div class="_logo _flex-center-y">
					<img src="<?=$site_logo_id?>" alt="" srcset="">
				</div>
				<div class="_cart_icon"></div>
		</div>
	</header>

	<main id="main" class="<?php flatsome_main_classes(); ?>">