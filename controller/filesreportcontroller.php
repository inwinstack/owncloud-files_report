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

    public function sendReport($path, $owner, $reportId) {
        $result = $this->data->send($path, $owner, $reportId);
        
        return new DataResponse(array('status' => $result));

    }

    /**
     * @NoCSRFRequired
      **/

    public function readReport() {
       $reports = $this->data->readReport();
       file_put_contents('123.txt', print_r($reports,true));
       
       return new TemplateResponse('files_report', 'part.content', array('reports' => $reports));
    
    }



}

?>
