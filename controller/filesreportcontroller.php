<?php
namespace OCA\Files_Report\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCA\Files_Report\Data;


class FilesReportController extends Controller {
    
    private $data;

    public function __construct($AppName, IRequest $request, Data $data) {
		parent::__construct($AppName, $request);

        $this->data = $data;
    }
    
    
    /**
     * @NoAdminRequired
     **/

    public function sendReport($path, $id,  $reportId) {
        $result = $this->data->send($path, $id, $reportId);
        
        return new DataResponse(array('status' => $result));

    }

    /**
     * @NoAdminRequired
     **/

    public function checkReport($path, $id) {
        $result = $this->data->check($path, $id);
        
        return new DataResponse(array('reported' => $result));

    }

     /**
     * @NoAdminRequired
     **/

    public function cancelReport($id) {
        $result = $this->data->cancel($id);
        
        return new DataResponse(array('status' => $result));

    }



    public function returnReport($id, $reason, $path, $owner) {
        $status = $reason != 'legal' ? Data::REPORT_STATE : Data::CANCEL_STATE;
        $result = $this->data->updateReport($id, $status, $path, $owner, $reason);
        
        return new DataResponse(array('status' => $result));

    }
    
    public function getReport($status) {
        $reports = $this->data->readReport($status);

        if($reports != 'error') {
            return new DataResponse( array('reports' => $reports));
        }
    }

  
    /**
     * @NoCSRFRequired
      **/

    public function readReport() {
        $reports = $this->data->readReport(Data::PENDING_STATE);

        if($reports != 'error') {
            return new TemplateResponse('files_report', 'main', array('reports' => $reports));
        }
    }



}

?>
