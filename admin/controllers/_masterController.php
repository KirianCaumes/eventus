<?php

namespace Eventus\Admin\Controllers;

/**
* MasterScreen is a parent class for all Screen
*
* @package  Admin/Screens
* @access   public
*/
abstract class MasterController { 
    protected $context; 
    protected $translationsJs;

    protected function __construct() {  
        $this->translationsJs = array(
            'defMessage' => __('This action is irreversible. Do you really want to delete the element?', 'eventus' ),
            'loading' => __('Loading in progress....', 'eventus' ),
            'selectAnImg' => __('Select the default team image', 'eventus' ),
            'selectImg' => __('Use this image', 'eventus' )
        );

        wp_register_script('eventus', plugin_dir_url( __FILE__ ).'/../../views/js/_eventus.js', '', '', true); 
        wp_localize_script('eventus', 'translations', $this->translationsJs);
        wp_enqueue_script('eventus');

        \Timber\Timber::$locations = plugin_dir_path( __FILE__ ).'../views/screens/';
        $this->context = \Timber\Timber::get_context();
        $this->context['notice'] = $this->showNotice();
        $this->context['isAdmin'] = current_user_can('administrator');
        $this->context['adminPostUrl'] = admin_url('admin-post.php');        

		$this->get = array(
            'message' => (isset($_GET['message']) ? $_GET['message'] : null),
			'action' => (isset($_GET['action']) ? $_GET['action'] : null),
            'teamId' => (isset($_GET['teamId']) ? $_GET['teamId'] : null),
            'clubId' => (isset($_GET['clubId']) ? $_GET['clubId'] : null),
            'seeked' => (isset($_GET['seeked']) ? $_GET['seeked'] : null),
            'err' => (isset($_GET['err']) ? $_GET['err'] : null),
		);
    }

    protected function render($template){   
        \Timber\Timber::render($template.".twig", $this->context);
    } 

    /**
    * Function to display the screen
    *
    * @return void
    * @access public
    */	
    protected function display(){
        echo "<div class='wrap'><h1 class='wp-heading-inline'>".__('Hello There', 'eventus')."</h1></div>";
        return;
    }
    
    /**
    * Show notice when action post has been done
    *
    * @return string    The notice message
    * @access protected
    */
    protected function showNotice(){
        $notices = array(
            'succesSyncMatch'=>array(
                'state' => "success", 
                'str'   => __('The match data has been synchronized.', 'eventus')
            ), 
            'warningSyncMatch'=>array(
                'state' => "warning", 
                'str'   => __('The match data was synchronized well, despite some errors. For more information, see ', 'eventus') . ' <a href="admin.php?page=eventus_logs">' . __('the logs', 'eventus') .'</a>.'
            ),
            'succesDelMatch'=>array(
                'state' => "success", 
                'str'   => __('The matches have been deleted.', 'eventus')
            ), 
            'succesUpMatch'=>array(
                'state' => "success", 
                'str'   => __('The matches have been updated.', 'eventus')
            ),  
            'succesUpHoursMatch'=>array(
                'state' => "success", 
                'str'   => __('The appointment times have been updated.', 'eventus')
            ),   
            'warningUpHoursMatch'=>array(
                'state' => "warning", 
                'str'   => __('The appointment times have been updated, despite some errors. For more information, see ', 'eventus') . ' <a href="admin.php?page=eventus_logs">' . __('the logs', 'eventus') .'</a>'
            ),  
            'succesUpTeam'=>array(
                'state' => "success", 
                'str'   => __('The team has been well updated.', 'eventus')
            ), 
            'succesNewTeam'=>array(
                'state' => "success", 
                'str'   => __('The team has been added well.', 'eventus')
            ),    
            'errorUpTeam'=>array(
                'state' => "error", 
                'str'   => __('The team could not be modified. Some fields are missing.', 'eventus')
            ), 
            'errorNewTeam'=>array(
                'state' => "error", 
                'str'   => __('The team could not be added. Some fields are missing.', 'eventus')
            ), 
            'succesDelTeam'=>array(
                'state' => "success", 
                'str'   => __('The team has been removed.', 'eventus')
            ), 
            'succesDelTeams'=>array(
                'state' => "success", 
                'str'   => __('The teams have been deleted.', 'eventus')
            ),  
            'succesUpClub'=>array(
                'state' => "success", 
                'str'   => __('The club has been updated.', 'eventus')
            ),
            'succesNewClub'=>array(
                'state' => "success", 
                'str'   => __('The club has been well added.', 'eventus')
            ),  
            'errorUpClub'=>array(
                'state' => "error", 
                'str'   => __('The club could not be modified. Some fields are missing.', 'eventus')
            ), 
            'errorNewClub'=>array(
                'state' => "error", 
                'str'   => __('The club could not be added. Some fields are missing.', 'eventus')
            ), 
            'succesDelClub'=>array(
                'state' => "success", 
                'str'   => __('The club has been deleted.', 'eventus')
            ), 
            'succesDelLog'=>array(
                'state' => "success", 
                'str'   => __('The logs have been deleted.', 'eventus')
            ), 
            'succesReset'=>array(
                'state' => "success", 
                'str'   => __('The plugin has been reset.', 'eventus')
            ), 
            'succesUpSet'=>array(
                'state' => "success", 
                'str'   => __('The parameters have been updated.', 'eventus')
            ),
            'noMapApiKey'=>array(
                'state' => "error", 
                'str'   => __('Please specify your Google Map API key in', 'eventus') . ' <a href="admin.php?page=eventus_admin">' . __('the parameters', 'eventus') .'</a>.'
            ),
            'succesMultiIcs'=>array(
                'state' => "success", 
                'str'   => __('The calendars have been updated.', 'eventus')
            ),
            'succesOneIcs'=>array(
                'state' => "success", 
                'str'   => __('The calendar has been updated.', 'eventus')
            ),
            'succesDelIcs'=>array(
                'state' => "success", 
                'str'   => __('The calendars have been deleted.', 'eventus')
            ),
            'errorSeeker'=>array(
                'state' => "error", 
                'str'   => __('Please select a club.', 'eventus')
            ),
            'succesSeeked'=>array(
                'state' => "success", 
                'str'   => __('The teams have been added well.', 'eventus')
            ),
        );
        if (array_key_exists('message', $_GET) && $_GET['message'] && $notices[$_GET['message']]) {
            return '<div class="notice notice-'.$notices[$_GET['message']]['state'].' is-dismissible"><p><strong>'.$notices[$_GET['message']]['str'].'</strong></p></div>'; 
        }
        return;
    }
}

?>