<a id="menuToggle" data-toggle="primary-menu">MENU <i class="fa fa-bars"></i></a>
<ul id="primary-menu" class="menu main-navigation" role="navigation" data-toggler=".expand">
  <?php wp_nav_menu( array( 
    'theme_location'  => 'primary',
    'container'       => false,
    'items_wrap'      => '%3$s',
    ) ); ?>
</ul>