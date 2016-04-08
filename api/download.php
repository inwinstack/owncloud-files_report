<?php
namespace OCA\Files_Report\API;
class Download{
    public static function forceDownloadFile() {
        if(\OC_User::isAdminuser(\OC_User::getUser())) {
            /**
            * Force download owner's file by admin role.
            *
            * @return bool
            */
               //expect $owner = 'admin'
               //expect $file = 'files/3/1.txt' | 'files/1.txt'
               $file = isset($_GET['filePath']) ? (string)$_GET['filePath'] : '';
               $owner = isset($_GET['owner']) ? (string)$_GET['owner'] : '';
               
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
               \OC_Files::get($dir, $files_list, $_SERVER['REQUEST_METHOD'] == 'HEAD');
        }
    }
}
       
?>
