<?php if( is_active_sidebar( 'header_widget_area' ) ):

?><aside class="header-widgets" itemscope itemtype="http://schema.org/WPSideBar">
	<?php dynamic_sidebar( 'header_widget_area' ); ?>
</aside>
<?php endif; ?>