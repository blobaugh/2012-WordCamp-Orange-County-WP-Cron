<?php
/*
 Plugin Name: 2012 WordCamp Orange County WP-Cron
 Plugin URI: https://github.com/blobaugh/2012-WordCamp-Orange-County-WP-Cron
 Description: Code to go along with my WP-Cron presentation at the 2012 Orange County WordCamp conference. To most easily visualize this code install the plugin and visit http://yourWPSite/wp-cron.php
 Version: 0.1
 Author: Ben Lobaugh
 Author URI: http://ben.lobaugh.net
 */


/*
 * wp_get_schedules() can be used to list all of the intervals currently available
 * in wp-cron
 * http://codex.wordpress.org/Function_Reference/wp_get_schedules
 * 
 * The following code can be used to display all the current schedule intervals
 */
//echo '<pre>'; print_r(wp_get_schedules()); echo '</pre>';

/*
 * The following intervals are built-in to wp-cron:
 *      hourly
 *      twicedaily
 *      daily
 * 
 * To more easily show wp-cron working let's add another interval of 5 seconds
 */
add_filter( 'cron_schedules', 'bl_add_cron_intervals' );

/*
 * This is the function that hooks into the cron_schedules filter. Note the
 * parameter. This paramter is an array containing all the current wp-cron
 * schedules that exist. Here we can add/update/remove any interval we wish
 * 
 * @param Array $schedules - List of current wp-cron intervals
 * @return Array - Potentially updated list of wp-cron intervals
 */
function bl_add_cron_intervals( $schedules ) {
    
    $schedules['5seconds'] = array( // Provide the programmatic name to be used in code
                    'interval' => 5, // Intervals are listed in seconds
                    'display' => __('Every 5 Seconds') // Easy to read display name
            );
    return $schedules; // Do not forget to give back the list of schedules!
}




/*
 * When creating a new scheduled event in WordPress you must give it a hook to
 * call when the scheduled time arrives. This is a custom hook that you must 
 * create yourself. Let's create that hook now
 * 
 * First parameter names the hook
 * Second parameter is the name of our function to call
 */
add_action( 'bl_cron_hook', 'bl_cron_exec' );


/*
 * Now to schedule the event. WordPress can be a little naive in scheduling
 * events, so you MUST check to make sure this event has not already been 
 * scheduled. Failure to do so can result in hundreds of entries for this
 * event existing in wp-cron
 */
if( !wp_next_scheduled( 'bl_cron_hook' ) ) {
    wp_schedule_event( time(), '5seconds', 'bl_cron_hook' );
}


/*
 * This is the function that is called from the custom hook 'bl_cron_hook' we
 * created earlier. It does really cool stuff!
 */
function bl_cron_exec() {
    echo "Oh Lookie! This is your scheduled cron, grinding out some hardcore tasks...And now a kitty!<br/><figure><img src='http://wpengine.com/wp-content/uploads/2012/04/lolcat-stealin-ur-heart.jpg'/><figcaption>Photo courtesy WCOC 2012, Stolen from WPEngine</figcaption></figure>";
}




/*
 * Whenever you create a wp-cron item in a plugin you will want to remove
 * any scheduled tasks when your plugin is deactivated or WordPress will
 * continue to attempt to execute the wp-cron task.
 * 
 * What we will do here is setup a function which will run if the plugin
 * is deactivated and remove the scheduled task
 */
register_deactivation_hook( __FILE__, 'bl_deactivate' );

/*
 * This is the deactivation function we setup previously
 */
function bl_deactivate() {
    $timestamp = wp_next_scheduled( 'bl_cron_hook' );
    wp_unschedule_event($timestamp, 'bl_cron_hook' );
    //echo '<pre>'; var_dump( _get_cron_array()); echo '</pre>';   
}


/*
 * Simple helper function that prints out all existing tasks in the wp-cron list
 */
function bl_print_tasks() {
    echo '<pre>'; print_r(_get_cron_array()); echo '</pre>';
}