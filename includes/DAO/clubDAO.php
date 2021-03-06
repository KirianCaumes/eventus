<?php 

namespace Eventus\Includes\DAO;
use Eventus\Includes\DTO as Entities;

/**
* ClubDAO is a class use to manage acces to the Database to get Club objects
*
* @package  Includes//DAO
* @access   public
*/
class ClubDAO extends MasterDAO {
    /**
    * @var Finder   $_instance  Var use to store an instance
    */
    private static $_instance;

    /**
    * Returns an instance of the object
    *
    * @return ClubDAO
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new ClubDAO();
        }
        return self::$_instance;
    }

    protected function __construct() {
        parent::__construct();
    }

    /**************************
    *********** GET ***********
    ***************************/
    /**
    * Return every clubs
    *
    * @return Club[] All the clubs that exist
    * @access public
    */
    function getAllClubs(){  
        $allClubs = [];
        $clubs = $this->wpdb->get_results("
            SELECT * 
            FROM 
                {$this->t1}
            WHERE
                club_season='{$this->season}'"
        ); 
        foreach($clubs as $row) { 
            $allClubs[] = new Entities\Club(
                $row->club_id, 
                $row->club_name, 
                $row->club_string, 
                $row->club_address, 
                $row->club_img,
                $row->club_season
            );
        }
        return $allClubs;
    }

    /**
    * Return the club corresponding to an id
    *
    * @param int        Id of the club
    * @return Club      All the clubs that exist with the ClubId
    * @access public
    */
    function getClubById($myClubId){   
        $row = $this->wpdb->get_row("
            SELECT * 
            FROM 
                {$this->t1} 
            WHERE 
                club_id=$myClubId AND
                club_season='{$this->season}'");
        return new Entities\Club(
            $row ? $row->club_id : 0, 
            $row ? $row->club_name : null, 
            $row ? $row->club_string : null, 
            $row ? $row->club_address : null, 
            $row ? $row->club_img : null,
            $row ? $row->club_season : null
        );
    }

    /**
    * Return informations by club id
    *
    * @param int        club id
    * @return string[]  Informations
    * @access public
    */
    function getInfosByClubId($idClub){  
        return $this->wpdb->get_row("
            SELECT 
                a.club_id, 
                count(b.team_id) as teamsNbr 
            FROM 
                {$this->t1} a 
            LEFT JOIN 
                {$this->t3} b 
            ON 
                a.club_id = b.team_clubId 
            WHERE 
                b.team_clubId=$idClub;");
    }

    /**
    * Return numbers of club
    *
    * @return string[]  Informations
    * @access public
    */
    function getNumbersClubs(){  
        return $this->wpdb->get_row("
            SELECT 
                count(DISTINCT club_id) as nbr_clubs
            FROM {$this->t1}
            WHERE
                club_season='{$this->season}';
        ");
    }
    /***************************
    ********** UPDATE **********
    ****************************/
    /**
    * Update a club
    *
    * @param Club       Club to be updated
    * @return void  
    * @access public
    */
    function updateClub($club){    
        if ($club->getId()){
            $data = array(
                'club_name' => $club->getName(), 
                'club_string' => $club->getString(), 
                'club_address' => $club->getAddress(), 
                'club_img' => $club->getImg()
            );
            $where = array('club_id' => $club->getId());
            $this->wpdb->update("{$this->t1}", $data, $where);
        }
    }

    /***************************
    ********** INSERT **********
    ****************************/
    /**
    * Insert a Club
    *
    * @param Club       Club to be inserted
    * @return int       Id of the club inserted      
    * @access public
    */
    function insertClub($club){
        if (!$club->getId()){            
            $data = array(
                'club_name' => $club->getName(), 
                'club_string' => $club->getString(), 
                'club_address' => $club->getAddress(), 
                'club_img' => $club->getImg(), 
                'club_season' => $club->getSeason()
            );
            $this->wpdb->insert("{$this->t1}", $data);
        }
        return $this->wpdb->insert_id;
    }

    /***************************
    ********** DELETE **********
    ****************************/
    /**
    * Delete a club
    *
    * @param int|null   Id of Club to be deleted
    * @return void    
    * @access public
    */
    function deleteClub($clubId = null){ 
        if ($clubId){
            $this->wpdb->query($this->wpdb->prepare("
                DELETE FROM 
                    {$this->t1} 
                WHERE  
                    club_id=%d", 
                $clubId)
            );
        } else {
            $this->wpdb->query($this->wpdb->prepare("
                DELETE FROM 
                    {$this->t1}
                WHERE 
                    1=%d", 
                1)
            );
        }
    }
}
?>