<?php 
// Example Cron Job :
// 54 0,22 * * 0,1,3,5 php ./www/wp-content/plugins/eventus/admin/business/cron.php >/dev/null 2>&1
namespace Eventus\Admin\Business;
use Eventus\Includes\DAO as DAO;

include_once __DIR__ . '/../../vendor/autoload.php';
include_once __DIR__ . '/../../includes/DTO/club.php';
include_once __DIR__ . '/../../includes/DTO/match.php';
include_once __DIR__ . '/../../includes/DTO/team.php';
include_once __DIR__ . '/../../includes/DAO/_masterDAO.php';
include_once __DIR__ . '/../../includes/DAO/teamDAO.php';
include_once __DIR__ . '/../../includes/DAO/clubDAO.php';
include_once __DIR__ . '/../../includes/DAO/matchDAO.php';
include_once __DIR__ . '/../../admin/business/helper/traitHelper.php';
include_once __DIR__ . '/../../admin/business/helper/staticHelper.php';
include_once __DIR__ . '/../../admin/business/finder.php';
include_once __DIR__ . '/../../../../../wp-config.php';
include_once __DIR__ . '/../../admin/business/ics.php';

date_default_timezone_set("Europe/Paris");
update_option('eventus_datetimesynch', date("Y-m-d H:i:s"), false);

$teams = DAO\TeamDAO::getInstance()->getAllTeams();

Finder::getInstance()->updateMatches($teams);

foreach ($teams as $team) {
	Ics::init(DAO\MatchDAO::getInstance()->getAllMatchesByTeamId($team->getId()));
}

// echo get_option("eventus_emailnotif");
if (get_option("eventus_emailnotif")){
	$message = "<a href='".get_option("siteurl")."/wp-admin/admin.php?page=eventus_logs'>Website's url</a><p>The update has been succesfully done with: <b>". count(file_exists(__DIR__ . '/../../finder.log') ? file(__DIR__ . '/../../finder.log') : array()) ."</b> issue(s), the <b>".date("d/m/Y")."</b> at <b>".date("H:i:s")."</b>.</p>";
	$content = explode("\n", @file_get_contents(__DIR__ . '/../../finder.log'));
	($content ? array_pop($content) : '' );
	$message .= ($content ? "<ul><li>".str_replace("[", "<b>[", str_replace("]", "]</b>", implode("</li><li>", $content)))."</ul>" : '');
	// echo $message;
	mail(
		get_option("eventus_emailnotif"), 
		"Eventus - Update ".date("d/m/Y H:i:s"), 
		$message, 
		"From: Eventus - ".parse_url(get_option("siteurl"))['host']." <eventus@".parse_url(get_option("siteurl"))['host']. ">\r\n" .
		"Reply-To: eventus@".get_option("siteurl") ."\r\n" .
		"Content-Type: text/html; charset=UTF-8"."\r\n".
		"X-Mailer: PHP/" . phpversion()
	);
	if (get_option("eventus_resetlog")) {
		file_put_contents(__DIR__ .'/../../finder.log', '');  
	}
}

?> 