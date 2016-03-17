<?php

namespace OCA\Files_Report;

use OCP\IUser;
use OCP\IUserSession;
use OCP\IDBConnection;


class Data {

    const PENDING_STATE = 0;
    const REPORT_STATE = 1;
    const CANCEL_STATE = 2;

    private $connection;
    private $userSession;
    
    public function __construct(IDBConnection $connection, IUserSession $userSession) {
	    $this->connection = $connection;
		$this->userSession = $userSession;
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
        $query->execute(array($owner, $user, $filepath ,$reason, time(), (60*60*24*180), self::PENDING_STATE));

        return 'success';
        
    }
    
    public function updateReport($id, $status) {
        $query = $this->connection->prepare('UPDATE  *PREFIX*file_reports SET status = ? WHERE id = ?');
        $query->execute(array($status, $id));

        return 'success';

    
    }

    public function readReport() {
        $reports = array();
        
        $query = $this->connection->prepare('SELECT id, owner, reporter, file_path, reason, status FROM *PREFIX*file_reports WHERE status = 0');
        $query->execute();
        

        while($row = $query->fetch()) {
            array_push($reports, $row);
        
        }
        
        return $reports;
    }

    private function readfilePath($path) {
        $query = $this->connection->prepare('SELECT path FROM *PREFIX*filecache JOIN *PREFIX*share ON *PREFIX*filecache.fileid = *PREFIX*share.file_source WHERE *PREFIX*share.file_target = ?');
        
        $query->execute(array($path));
        $row = $query->fetch();

        return substr($row['path'], 5);
    
    }

}


?>
