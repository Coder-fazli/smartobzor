<?php
/**
 * The template for displaying the header
 *
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta name="google-site-verification" content="EVv8JI1rI99r26zArKjlnhP4Bh0y4Jy9JBpxNncgoyw" />
	<meta name="yandex-verification" content="a1ad937b68e63de0" />
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(97490064, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/97490064" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
	<?php wp_head(); ?>
</head>

<body id="tie-body" <?php body_class(); ?>>

<?php wp_body_open(); ?>
<!-- Paste next code to the place where in-read ad should appear -->
<div class="tgx-rlf" data-rlf-id="20976" data-rlf-auto="1" data-rlf-flt="1" data-rlf-dock="0" data-rlf-mob="1" data-rlf-align="lb" data-rlf-fw="320"></div>
 
<!-- Paste next line before closing BODY tag -->
<div class="background-overlay">

	<div id="tie-container" class="site tie-container">

		<?php do_action( 'TieLabs/before_wrapper' ); ?>

		<div id="tie-wrapper">

			<?php

				TIELABS_HELPER::get_template_part( 'templates/header/load' );

				do_action( 'TieLabs/before_main_content' );