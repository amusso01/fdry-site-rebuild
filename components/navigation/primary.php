<?php

/**
 * Primary Nav
 * 
 * @author Andrea Musso
 * 
 * @package Foundry
 */

if (has_nav_menu('mainmenu')) :
  wp_nav_menu([
    'theme_location'    => 'mainmenu',
    'menu_id'           => 'menu_main',
    'container'         => 'nav',
    'container_class'   => 'site-header__menu primary-menu',
    'depth'             => 1
  ]);
endif;
