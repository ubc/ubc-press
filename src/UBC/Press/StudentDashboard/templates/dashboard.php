<?php
/**
 * The template for displaying the general dashboard.
 *
 * @package wp-clf-lite
 */

$current_user = wp_get_current_user();
$course_title			= ( empty( get_theme_mod( 'course_title' ) ) ? 'Please Enter Course Tilte' : get_theme_mod( 'course_title' ) );
$home_url				= home_url( '/' );

get_header();
?>

<div id="primary" class="content-area primary">
    <main id="main" class="site-main dashboard">

        <?php tha_entry_before(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php tha_entry_top(); ?>

            <div class="dashboard-header">
            	<div class="row expanded">
		            <header class="entry-header column small-12">
		                <h1 class="course-title"><a href="<?php echo esc_url( $home_url );?>" title="Course home"><?php echo esc_html( $course_title ); ?></a></h1>
			        </header>
	           	 	<!-- .entry-header -->
			    </div>

	            <div class="row expanded meta">
	            	<section class="small-12 medium-5 column user">
	            	<h3>Course instructor</h3>
	            		<div class="row">
		            		<div class="small-8 medium-7 large-9 small-order-2 column">
		            			<p class="user-name">Phillip J. Fry, <small class="user-title">Course instructor</small></p>
				            	<p><a href="mailto:fry@ubc.ca">fry@ubc.ca</a><br>
				            	604 555 555</p>
		            		</div>
		            		<!-- user info container -->
		            		<div class="small-4 medium-5 large-3 small-order-1 column">
		            			<img class="avatar aligncenter size-thumbnail wp-image-204" src="http://localhost/wordpress-multisite/brabbins/wp-content/uploads/sites/8/2016/05/12XeSbixxv8-150x150.jpg" alt="12XeSbixxv8" width="150" height="150" />
		            		</div>
		            		<!-- img container -->
		            	</div>
		            	<!-- .row -->
	            	</section>
	            	<!-- .user -->
	            	<section class="small-12 medium-7 column course-progress">
	            		<h3>Course progress</h3>
						<div class="progress" role="progressbar" tabindex="0" aria-valuenow="65" aria-valuemin="0" aria-valuetext="65 percent" aria-valuemax="100">
							<span class="progress-meter" style="width: 65%">
								<p class="progress-meter-text">65%</p>
							</span>
							<p><small>[Completed: 20/30]</small></p>
							<!-- .progress-meter -->
						</div>
						<!-- .progress -->
	            	</section>
	            	<!-- .course-progress -->
	            </div>
	            <!-- .row.expanded -->
	        </div>
	        <!-- .dashboard-header -->
            <section class="entry-content">
                <ul class="tabs  expanded row small-collapse text-center align-center" id="course-dashbord-tabs" data-tabs>
                    <li class="tabs-title column is-active"><a href="#panel1v" aria-selected="true"><span class="dashicons dashicons-calendar-alt"></span> <span class="hide-for-small-only">Calendar</span></a>
                    </li>
                    <li class="tabs-title column"><a href="#panel2v"><span class="dashicons dashicons-edit"></span> <span class="hide-for-small-only">Notes</span></a>
                    </li>
                    <li class="tabs-title column"><a href="#panel3v"><span class="dashicons dashicons-heart"></span> <span class="hide-for-small-only">Saved</span></a>
                    </li>
                    <li class="tabs-title column"><a href="#panel4v"><span class="dashicons dashicons-groups"></span> <span class="hide-for-small-only">Groups</span></a>
                    </li>
                    <li class="tabs-title column"><a href="#panel5v"><span class="dashicons dashicons-megaphone"></span> <span class="hide-for-small-only">Discussion</span></a>
                    </li>
                    <li class="tabs-title column notifications yes"><a href="#panel6v"><span class="dashicons dashicons-warning"></span> <span class="hide-for-small-only">Notificatons</span></a>
                    </li>
                </ul>
                <div class="tabs-content row expanded" data-tabs-content="course-dashbord-tabs">
                    <div class="tabs-panel column is-active" id="panel1v">
                    	<div class="row">
	                        <section class="upcoming small-order-1 small-12 medium-6 large-4 column">
	                            <header>
	                                <h2>Upcoming</h2>
	                            </header>
	                            <div class="empty-state callout grey text-center">
		                            <p><span class="dashicons dashicons-smiley warning-txt icon"></span></p>
		                            <p>You must be feeling good about yourself since it appears you have nothing due, <strong>OR</strong> the list is still loading. A glorious WOOHOO.</p>
		                            <p><a class="button">Main course page</a></p>
		                            <p><small>Try not worry about it, as I am sure you are, this area will populate once an instructor starts inputing assignments, quizzes and/or other such edumacation learning material.</small></p>
	                            </div>
	                            <!-- .empty-state -->
	                            <header class="callout">
	                            	<h4 class="section-title"><a href="#">This assignment will be due soon</a></h4>
		                            <p><small>Due: <strong>Friday, April 20th, 2016</small></strong></p>
		                            <p><a class="tiny success button" href="#">View assignment</a></p>
	                            </header>

	                            <header class="callout">
	                            	<h4 class="section-title"><a href="#">This lecture is happening soon</a></h4>
		                            <p><small>Due: April 20th, 2016 3pm</small></p>
		                            <p><a class="tiny success button" href="#">View lecture</a></p>
	                            </header>

								<hr>

	                            <header class="callout">
	                            	<h4 class="section-title"><a href="#">This lecture is happening soon</a></h4>
		                            <p><small>Due: April 20th, 2016 3pm</small></p>
		                            <p><a class="tiny success button" href="#">View lecture</a></p>
	                            </header>
	                        </section>
	                        <!-- .upcoming -->
	                        <section class="calendar small-order-2 small-12 medium-6 large-8 column">
	                            <header>
	                                <h2>Calendar</h2>
	                            </header>
	                            <div class="empty-state callout grey text-center large">
		                            <p><span class="dashicons dashicons-tickets-alt icon secondary-txt"></span></p>
		                            <p>Two free tickets to FREEDOM! Nothing scheduled.</p>
		                            <p><a class="button">Main course page</a></p>
		                            <p><small>Try not worry about it, as I am sure you are, this area will populate once an instructor starts inputing assignments, quizzes and/or other such edumacation learning material.</small></p>
	                            </div>
	                            <!-- .empty-state -->
	                        </section>
	                        <!-- .calendar -->
	                    </div><!-- .row -->
                    </div>
                    <div class="tabs-panel column notes" id="panel2v">
                    	<header>
                    		<h2>Notes</h2>
                    		<p class="lead"></span> This section contains notes that have been written in subsections. <a href="#">Learn more</a>.</p>
                    	</header>
                    	<!-- <p><a class="button success" href="#">See all notes</a></p> -->
                    	<div class="row">
	                    	<section id="note-toggle" class="small-12 medium-12 large-6 column" data-toggler=".expanded">
	                    		<div id="note-toggle" class="callout">
	                    			<p class="note-meta">Filed under "<a href="#">Section tile for some section</a>".<br>
	                    			<small>Last updated April 30th, 2016</small></p>
	                    			<div class="button-group">
	                    				<a class="button read-note" data-toggle="note-toggle"><span class="button-txt">Read note</span> <span class="dashicons dashicons-no-alt" aria-hidden="true"></span></a>
	                    				<a class="button secondary" data-toggle="note-toggle"><span class="dashicons dashicons-media-text"></span> Print</a>
	                    				<a class="button secondary" data-toggle="note-toggle"><span class="dashicons dashicons-download"></span> Download</a>
	                    				<a class="button secondary hollow float-right" data-toggle="note-toggle"><span class="dashicons dashicons-yes"></span> Print list</a>
	                    			</div>
	                    			<!-- .button-group -->
	                    			<div class="note">
		                    			<p>Bender?! You stole the atom. And yet you haven't said what I told you to say! How can any of us trust you? Alright, let's mafia things up a bit. Joey, burn down the ship. Clamps, burn down the crew. Ooh, name it after me!</p>
										<p>I'm Santa Claus! Large bet on myself in round one. Noooooo! You know the worst thing about being a slave? <strong> They make you work, but they don't pay you or let you go.</strong> <em> Well, then good news!</em> It's a suppository.</p>
										<p>Now Fry, it's been a few years since medical school, so remind me. Disemboweling in your species: fatal or non-fatal? I am the man with no name, Zapp Brannigan! That's not soon enough! Who's brave enough to fly into something we all keep calling a death sphere?</p>
										<p>You seem malnourished. Are you suffering from intestinal parasites? Belligerent and numerous. Oh, how awful. Did he at least die painlessly? …To shreds, you say. Well, how is his wife holding up? …To shreds, you say.</p>
										<ol>
										   <li>I haven't felt much of anything since my guinea pig died.</li>
										   <li>Tell them I hate them.</li>
										   <li>And yet you haven't said what I told you to say! How can any of us trust you?</li>
										</ol>
									<div class="button-group">
	                    				<a class="button read-note" data-toggle="note-toggle"><span class="button-txt">Read note</span> <span class="dashicons dashicons-no-alt" aria-hidden="true"></span></a>
	                    				<a class="button secondary" data-toggle="note-toggle"><span class="dashicons dashicons-media-text"></span> Print</a>
	                    				<a class="button secondary" data-toggle="note-toggle"><span class="dashicons dashicons-download"></span> Download</a>
	                    			</div>
	                    			<!-- .button-group -->
									</div>
									<!-- .note -->
	                    		</div>
	                    		<!-- .callout -->
	                    	</section>

							<section class="small-12 medium-12 large-6 column">
	                    		<div id="note-toggle" class="callout" data-toggler=".expanded">
	                    			<p><small>Note from section "<strong><em><a href="#">Section tile for some section</a></em></strong>".<br>
	                    			Last updated <em>April 30th, 2016</em></small></p>
	                    			<div class="button-group small">
	                    				<a class="button" data-toggle="note-toggle">Read note</a>
	                    				<a class="button secondary" data-toggle="note-toggle">Print</a>
	                    				<a class="button secondary" data-toggle="note-toggle">Download</a>
	                    			</div>
	                    			<!-- .button-group -->
	                    			<div class="note">
		                    			<p>Bender?! You stole the atom. And yet you haven't said what I told you to say! How can any of us trust you? Alright, let's mafia things up a bit. Joey, burn down the ship. Clamps, burn down the crew. Ooh, name it after me!</p>
										<p>I'm Santa Claus! Large bet on myself in round one. Noooooo! You know the worst thing about being a slave? <strong> They make you work, but they don't pay you or let you go.</strong> <em> Well, then good news!</em> It's a suppository.</p>
										<p>Now Fry, it's been a few years since medical school, so remind me. Disemboweling in your species: fatal or non-fatal? I am the man with no name, Zapp Brannigan! That's not soon enough! Who's brave enough to fly into something we all keep calling a death sphere?</p>
										<p>You seem malnourished. Are you suffering from intestinal parasites? Belligerent and numerous. Oh, how awful. Did he at least die painlessly? …To shreds, you say. Well, how is his wife holding up? …To shreds, you say.</p>
										<ol>
										   <li>I haven't felt much of anything since my guinea pig died.</li>
										   <li>Tell them I hate them.</li>
										   <li>And yet you haven't said what I told you to say! How can any of us trust you?</li>
										</ol>
										<div class="button-group small">
		                    				<a class="button" data-toggle="note-toggle">Read note</a>
		                    				<a class="button secondary" data-toggle="note-toggle">Print</a>
		                    				<a class="button secondary" data-toggle="note-toggle">Download</a>
		                    			</div>
		                    			<!-- .button-group -->
									</div>
									<!-- .note -->
	                    		</div>
	                    		<!-- .callout -->
	                    	</section>

							<section class="small-12 medium-12 large-6 column">
	                    		<div id="note-toggle" class="callout" data-toggler=".expanded">
	                    			<p><small>Note from section "<strong><em><a href="#">Section tile for some section</a></em></strong>".<br>
	                    			Last updated <em>April 30th, 2016</em></small></p>
	                    			<div class="button-group small">
	                    				<a class="button" data-toggle="note-toggle">Read note</a>
	                    				<a class="button secondary" data-toggle="note-toggle">Print</a>
	                    				<a class="button secondary" data-toggle="note-toggle">Download</a>
	                    			</div>
	                    			<!-- .button-group -->
	                    			<div class="note">
		                    			<p>Bender?! You stole the atom. And yet you haven't said what I told you to say! How can any of us trust you? Alright, let's mafia things up a bit. Joey, burn down the ship. Clamps, burn down the crew. Ooh, name it after me!</p>
										<p>I'm Santa Claus! Large bet on myself in round one. Noooooo! You know the worst thing about being a slave? <strong> They make you work, but they don't pay you or let you go.</strong> <em> Well, then good news!</em> It's a suppository.</p>
										<p>Now Fry, it's been a few years since medical school, so remind me. Disemboweling in your species: fatal or non-fatal? I am the man with no name, Zapp Brannigan! That's not soon enough! Who's brave enough to fly into something we all keep calling a death sphere?</p>
										<p>You seem malnourished. Are you suffering from intestinal parasites? Belligerent and numerous. Oh, how awful. Did he at least die painlessly? …To shreds, you say. Well, how is his wife holding up? …To shreds, you say.</p>
										<ol>
										   <li>I haven't felt much of anything since my guinea pig died.</li>
										   <li>Tell them I hate them.</li>
										   <li>And yet you haven't said what I told you to say! How can any of us trust you?</li>
										</ol>
										<div class="button-group small">
		                    				<a class="button" data-toggle="note-toggle">Read note</a>
		                    				<a class="button secondary" data-toggle="note-toggle">Print</a>
		                    				<a class="button secondary" data-toggle="note-toggle">Download</a>
		                    			</div>
		                    			<!-- .button-group -->
									</div>
									<!-- .note -->
	                    		</div>
	                    		<!-- .callout -->
	                    	</section>

							<section class="small-12 medium-12 large-6 column">
	                    		<div id="note-toggle" class="callout" data-toggler=".expanded">
	                    			<p><small>Note from section "<strong><em><a href="#">Section tile for some section</a></em></strong>".<br>
	                    			Last updated <em>April 30th, 2016</em></small></p>
	                    			<div class="button-group small">
	                    				<a class="button" data-toggle="note-toggle">Read note</a>
	                    				<a class="button secondary" data-toggle="note-toggle">Print</a>
	                    				<a class="button secondary" data-toggle="note-toggle">Download</a>
	                    			</div>
	                    			<!-- .button-group -->
	                    			<div class="note">
		                    			<p>Bender?! You stole the atom. And yet you haven't said what I told you to say! How can any of us trust you? Alright, let's mafia things up a bit. Joey, burn down the ship. Clamps, burn down the crew. Ooh, name it after me!</p>
										<p>I'm Santa Claus! Large bet on myself in round one. Noooooo! You know the worst thing about being a slave? <strong> They make you work, but they don't pay you or let you go.</strong> <em> Well, then good news!</em> It's a suppository.</p>
										<p>Now Fry, it's been a few years since medical school, so remind me. Disemboweling in your species: fatal or non-fatal? I am the man with no name, Zapp Brannigan! That's not soon enough! Who's brave enough to fly into something we all keep calling a death sphere?</p>
										<p>You seem malnourished. Are you suffering from intestinal parasites? Belligerent and numerous. Oh, how awful. Did he at least die painlessly? …To shreds, you say. Well, how is his wife holding up? …To shreds, you say.</p>
										<ol>
										   <li>I haven't felt much of anything since my guinea pig died.</li>
										   <li>Tell them I hate them.</li>
										   <li>And yet you haven't said what I told you to say! How can any of us trust you?</li>
										</ol>
										<div class="button-group small">
		                    				<a class="button" data-toggle="note-toggle">Read note</a>
		                    				<a class="button secondary" data-toggle="note-toggle">Print</a>
		                    				<a class="button secondary" data-toggle="note-toggle">Download</a>
		                    			</div>
		                    			<!-- .button-group -->
									</div>
									<!-- .note -->
	                    		</div>
	                    		<!-- .callout -->
	                    	</section>

                    	</div>
                        <p><a class="button success large" href="#">View all course notes</a></p>
                    </div>
                    <div class="tabs-panel column saved" id="panel3v">
                    <h2>Saved</h2>
	                    <div class="row">
	                    	<section class="small-12 medium-6 large-4 column">
	                    		<div class="callout">
		                    	<h4>Saved Component</h4>
		                    	<h5>Saved from <a href="">Section title</a></h5>
			                    	<div class="button-group tiny">
			                    		<a href="#" class="button success tiny">View component</a> <a href="#" class="button tiny">Delete</a>
			                    	</div>
			                    	<!-- .button-group -->
		                    	</div>
		                    	<!-- .callout -->
	                    	</section>
	                    	<section class="small-12 medium-6 large-4 column">
	                    		<div class="callout">
		                    	<h4>Saved Component</h4>
		                    	<h5>Saved from <a href="">Section title</a></h5>
			                    	<div class="button-group tiny">
			                    		<a href="#" class="button success tiny">View component</a> <a href="#" class="button tiny">Delete</a>
			                    	</div>
			                    	<!-- .button-group -->
		                    	</div>
		                    	<!-- .callout -->
	                    	</section>
	                    	<section class="small-12 medium-6 large-4 column">
	                    		<div class="callout">
		                    	<h4>Saved Component</h4>
		                    	<h5>Saved from <a href="">Section title</a></h5>
			                    	<div class="button-group tiny">
			                    		<a href="#" class="button success tiny">View component</a> <a href="#" class="button tiny">Delete</a>
			                    	</div>
			                    	<!-- .button-group -->
		                    	</div>
		                    	<!-- .callout -->
	                    	</section>
	                    	<section class="small-12 medium-6 large-4 column">
	                    		<div class="callout">
		                    	<h4>Saved Component</h4>
		                    	<h5>Saved from <a href="">Section title</a></h5>
			                    	<div class="button-group tiny">
			                    		<a href="#" class="button success tiny">View component</a> <a href="#" class="button tiny">Delete</a>
			                    	</div>
			                    	<!-- .button-group -->
		                    	</div>
		                    	<!-- .callout -->
	                    	</section>
	                    	<section class="small-12 medium-6 large-4 column">
	                    		<div class="callout">
		                    	<h4>Saved Component</h4>
		                    	<h5>Saved from <a href="">Section title</a></h5>
			                    	<div class="button-group tiny">
			                    		<a href="#" class="button success tiny">View component</a> <a href="#" class="button tiny">Delete</a>
			                    	</div>
			                    	<!-- .button-group -->
		                    	</div>
		                    	<!-- .callout -->
	                    	</section>
	                    </div>
                    </div>
                    <div class="tabs-panel column" id="panel4v">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    </div>
                    <div class="tabs-panel column" id="panel5v">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    </div>
                    <div class="tabs-panel column align-center notifications" id="panel6v">
                    	<header>
                    		<h2>Notifications</h2>
                    	</header>
	                    <div class="row expanded">
	                    	<section class="small-12 medium-6 large-4 column">
	                    		<div class="callout warning small">
	                    			<div class="row">
	                    				<p class="small-6 column notifier"><a href="#"><strong>Homer Simpson</strong></a><br>
	                    				<small>Professor</small></p>
		                    			<p class="small-6 column text-right date-time"><small>June 25th, 2016 2:32pm</small></p>
			                    	</div>
			                    	<p>Danish chupa chups jelly marzipan lemon drops. Chocolate fruitcake sugar plum apple pie tootsie roll. Jelly cookie gummi bears dessert tootsie roll.</p>
			                    	<p><a href="#" class="button success small">Mark as read</a></p>
		                    	</div>
		                    	<!-- .callout -->
	                    	</section>

	                    	<section class="small-12 medium-6 large-4 column">
	                    		<div class="callout warning">
	                    			<div class="row">
	                    				<p class="small-6 column notifier"><a href="#"><strong>Philip J. Fry</strong></a><br>
	                    				<small>Student</small></p>
		                    			<p class="small-6 column text-right date-time"><small>June 25th, 2016 2:32pm</small></p>
			                    	</div>
			                    	<p>Prow scuttle parrel provost Sail ho shrouds spirits boom mizzenmast yardarm. Pinnace holystone mizzenmast quarter crow's nest nipperkin grog yardarm hempen halter furl.</p>
			                    	<p><a href="#" class="button success small">Mark as read</a></p>
		                    	</div>
		                    	<!-- .callout -->
	                    	</section>

	                        <section class="small-12 medium-6 large-4 column">
	                    		<div class="callout">
	                    			<div class="row">
	                    				<p class="small-6 column notifier"><a href="#"><strong>Steve Rogers</strong></a><br>
	                    				<small>Avenger</small></p>
		                    			<p class="small-6 column text-right date-time"><small>June 25th, 2016 2:32pm</small></p>
			                    	</div>
			                    	<p>Prow scuttle parrel provost Sail ho shrouds spirits boom mizzenmast yardarm. Pinnace holystone mizzenmast quarter crow's nest nipperkin grog yardarm hempen halter furl.</p>
			                    	<p><a href="#" class="button small">Mark as unread</a></p>
		                    	</div>
		                    	<!-- .callout -->
	                    	</section>

	                    </div>
	                    <!-- .row -->
                    </div>
                    <!-- .tabs-panel.notifications.column -->
                </div>
                <!-- .tabs-content -->
            </section>
            <!-- .entry-content -->

            <footer class="entry-footer">
                <?php edit_post_link( esc_html__( 'Edit', 'wp-clf-lite' ), '<span class="edit-link">', '</span>' ); ?>
            </footer>
            <!-- .entry-footer -->
            <?php tha_entry_bottom(); ?>
        </article>
        <!-- #post-## -->
        <?php tha_entry_after(); ?>

    </main>
    <!-- #main -->
</div>
<!-- #primary -->

<?php get_footer(); ?>
