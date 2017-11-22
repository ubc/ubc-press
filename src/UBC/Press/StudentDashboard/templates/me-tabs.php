<?php
$tab_names = \UBC\Press\StudentDashboard\Utils::ubc_press_tab_names();
$i = 0;
?>

<nav>
	<ul class="tabs text-center row-smll-collapse align-items-center" data-deep-link="true" data-update-history="true" data-deep-link-smudge="500" id="course-dashbord-tabs" data-tabs>
		<?php foreach ( $tab_names as $tab_name ) :
			$is_active = ( 0 === $i++ ? 'is-active' : '' );
			$icon  = ( 'progress' === $tab_name ? 'checkmark-circle' : $tab_name );
			?>
			<li class="tabs-title <?php echo esc_attr( $is_active )?>">
				<a href="#dashboard-<?php echo esc_attr( $tab_name ); ?>"><svg class="ui-icon <?php echo esc_attr( $icon ); ?>" aria-hidden="true"><use xlink:href="#<?php echo esc_attr( $icon ); ?>"></use></svg> <span class="hide-for-small-only"><?php echo esc_html( $tab_name ); ?></span></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
