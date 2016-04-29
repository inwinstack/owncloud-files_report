<?php

namespace OCA\Files_Report;

use OCP\DB;
use OCP\Util;
use OCP\IUser;
use OCP\IUserSession;
use OCP\IDBConnection;
use OCA\Files_Report\Constants;
use OCA\Files_Report\ForceDelete;
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

    public function send($path, $id, $reason) {
        $user = $this->userSession->getUser();
        $filepath = $this->readfilePath($path);
        $owner = $this->getOwner($id);

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

    public function check($path, $id) {
        $filepath = $this->readfilePath($path);
        $owner = $this->getOwner($id);
        
        

        $query = $this->connection->prepare('SELECT id, reason FROM *PREFIX*file_reports WHERE status = 0 AND owner = ? AND file_path = ?');
        $result = $query->execute(array($owner, $filepath));

        if(DB::isError($result)) {
            Util::writeLog('FilesReport', DB::getErrorMessage($result), Util::ERROR);

            return 'error';

        } else {
            $row = $query->fetch();

            return $row;
        }
    
    }

    public function cancel($id) {
        
        $query = $this->connection->prepare('DELETE FROM *PREFIX*file_reports WHERE id = ?');
        $result = $query->execute(array((int)$id));

        if(DB::isError($result)) {
            Util::writeLog('FilesReport', DB::getErrorMessage($result), Util::ERROR);

            return 'error';

        }

        return 'success';
    
    }

    
    public function updateReport($id, $status, $path, $owner, $reason) {
        if($status == Data::REPORT_STATE) {
            $state = ForceDelete::forceDeleteOwnerFile($owner, $path);
            $state && $this->addActivityData($owner, array(substr($path, 5), $reason));
        } else {
            $reason = 4;
        }

        $query = $this->connection->prepare('UPDATE *PREFIX*file_reports SET status = ?, reason = ?, timestamp = ? WHERE id = ?');
        $result = $query->execute(array($status, $reason, time(), $id));

        if(DB::isError($result) || !$state) {

            return 'error';
        } else {

            return 'success';
        }

    }

    public function readReport($status) {
        $reports = array();
        
        $query = $this->connection->prepare('SELECT id, owner, reporter, file_path, reason, status,timestamp FROM *PREFIX*file_reports WHERE status = ?');
        $result = $query->execute(array($status));
        if(DB::isError($result)) {
			Util::writeLog('FilesReport', DB::getErrorMessage($result), Util::ERROR);
            
            return 'error';
        } else {
            while($row = $query->fetch()) {
                $parts = split("/", $row['file_path']);
                $row['file_name'] = $parts[count($parts) - 1];
                $row['reason'] = self::$reason_arr[(int)$row['reason']];
                $row['time'] = date('Y-m-d',$row['timestamp']);
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

    private function getOwner($id) {
        $query = $this->connection->prepare('SELECT id FROM *PREFIX*storages JOIN *PREFIX*filecache ON *PREFIX*filecache.storage = *PREFIX*storages.numeric_id WHERE *PREFIX*filecache.fileid = ?');

        $query->execute(array($id));
        $row = $query->fetch();

        return substr($row['id'],6);

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

     public function forceDownloadFile($owner, $filePath) {
       //expect $owner = 'admin'
       //expect $file = 'files/3/1.txt' | 'files/1.txt'
       
       file_put_contents('test.txt', $file.' '.$owner);
       if ($file == '' || $owner == ''){
           return false;
       }
       //\OC\Files\Filesystem::tearDown();
       //\OC\Files\Filesystem::initMountPoints($owner);
       \OC\Files\Filesystem::init($owner,"/$owner/files");
       //$view = new \OC\Files\View("/$owner/files");
       //$view = \OC\Files\Filesystem::getView();
       
       $dirs = explode("/", $file);
       $rootDir = $dirs[0];
       $filterFilePath = preg_replace("/^$rootDir\//",'',$file);
       $dir = dirname($filterFilePath);
       $fileName = basename($filterFilePath);
       $files_list = array($fileName);
       OC_Files::get($dir, $files_list, $_SERVER['REQUEST_METHOD'] == 'HEAD');
       
    }


}


?>
