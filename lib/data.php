<?php

namespace OCA\Files_Report;

use OCP\DB;
use OCP\Util;
use OCP\IUser;
use OCP\IUserSession;
use OCP\IDBConnection;
use OCA\Files_Report\Constants;
use OCA\Files_Report\ForceDelte;
use OC\Activity\Event;

class Data extends Constants {

    private $connection;
    private $userSession;
    private $activityData;

    public function __construct(IDBConnection $connection, IUserSession $userSession, \OCA\Activity\Data $activityData) {
	    $this->connection = $connection;
		$this->userSession = $userSession;
        $this->activityData = $activityData;
    }

    public function send( $path, $owner, $reason) {
        $user = $this->userSession->getUser();
        $filepath = $this->readfilePath($path);

		if ($user instanceof IUser) {
			$user = $user->getUID();
		} else {
            return 'error';
        }
         
        
        $query = $this->connection->prepare('INSERT INTO *PREFIX*file_reports(owner,reporter,file_path,reason,timestamp,ttl,status) VALUES(?,?,?,?,?,?,?)');
        $result = $query->execute(array($owner, $user, $filepath ,$reason, time(), (60*60*24*180), self::PENDING_STATE));
        if(DB::isError($result)) {
			Util::writeLog('FilesReport', DB::getErrorMessage($result), Util::ERROR);
            
            return 'error';
        }

        return 'success';
        
    }
    
    public function updateReport($id, $status, $path, $owner) {
        $query = $this->connection->prepare('UPDATE *PREFIX*file_reports SET status = ? WHERE id = ?');
        $result = $query->execute(array($status, $id));
        if(DB::isError($result)) {
			Util::writeLog('FilesReport', DB::getErrorMessage($result), Util::ERROR);
            
            return 'error';
        } else {
            if($status == Data::REPORT_STATE ) {
                ForceDelete::forceDeleteOwnerFile($owner, $path);
                $this->addActivityData($owner, array(substr($path, 5)));
            }
            return 'success';
        }
    }

    public function readReport() {
        $reports = array();
        
        $query = $this->connection->prepare('SELECT id, owner, reporter, file_path, reason, status FROM *PREFIX*file_reports WHERE status = ?');
        $result = $query->execute(array(self::PENDING_STATE));
        if(DB::isError($result)) {
			Util::writeLog('FilesReport', DB::getErrorMessage($result), Util::ERROR);
            
            return 'error';
        } else {
            while($row = $query->fetch()) {
                $parts = split("/", $row['file_path']);
                $row['file_name'] = $parts[count($parts) - 1];
                array_push($reports, $row);
            
            }
            
            return $reports;
        }
        
    }

    private function readfilePath($path) {
        $query = $this->connection->prepare('SELECT path FROM *PREFIX*filecache JOIN *PREFIX*share ON *PREFIX*filecache.fileid = *PREFIX*share.file_source WHERE *PREFIX*share.file_target = ?');
        
        $query->execute(array($path));
        $row = $query->fetch();

        return $row['path'];
    
    }

    private function addActivityData($user, $params) {
       $event = new Event();
       $event->setApp('files_report')
            ->setType('reported')
            ->setAffectedUser($user)
            ->setAuthor($this->userSession->getUser()->getUID())
            ->setTimestamp(time())
            ->setSubject('reported_with', $params);

        $this->activityData->send($event);
        
        //$latestSend = time() + (60*10);//after 10min send email
	    //$this->activityData->storeMail($event, $latestSend);

    }

}


?>
