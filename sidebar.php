<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

//* Handles the primary sidebar structure.
printf( '<aside %s>', bizznis_attr( 'sidebar-primary' ) );
echo bizznis_sidebar_title( 'sidebar' );
do_action( 'bizznis_sidebar' );
echo '</aside>' ."\n";