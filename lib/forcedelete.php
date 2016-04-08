<?php
namespace OCA\Files_Report;

class ForceDelete {
    /**
    * Force owner's delete file/folder by admin role.
    *
    * @param string $owner
    * @param string $file
    * @return bool
    */
    public static function forceDeleteOwnerFile($owner,$file){
       \OCP\Util::writeLog('Duncan','=$owner='.$owner, \OCP\Util::INFO);
       \OCP\Util::writeLog('Duncan','=$file='.$file, \OCP\Util::INFO);
       //$file = files/1.txt
       //$file = files/2
       //$file = files/3/1.txt

       //$owner = 'test';
       //$file = 'files/1.txt';

       //Prepare filter the file path.
       $dirs = explode("/", $file);
       $rootDir = $dirs[0];
       $filterFilePath = preg_replace("/^$rootDir\//",'',$file);

       // Step1: Init Mountpoints and new owner's view.
       //\OC\Files\Filesystem::tearDown();
       \OC\Files\Filesystem::initMountPoints($owner);
       $view = new \OC\Files\View("/$owner/files");
       
       
       if (!$view->unlockFile($filterFilePath,\OCP\Lock\ILockingProvider::LOCK_EXCLUSIVE)){
           \OCP\Util::writeLog('module name',"When force unlock file ($filterFilePath) failed 2.", \OCP\Util::ERROR);
           return false;
       }

       // Step3: Delete the file's priview file.
       self::deletePreviewFiles($owner,$view,$file);


       // Step4: Delete the file's veriosn file.
       if(!\OC_App::isEnabled('files_versions')){
           self::deleteVersionFiles($owner,$filterFilePath);
       }

       // Step5: Delete the file.
       if (!$view->unlink($filterFilePath) || $view->file_exists($filterFilePath)){
           \OCP\Util::writeLog('module name',"When force delete $filterFilePath file failed 3.", \OCP\Util::EORROR);
           return false;
       }
        

       return true;
    }
    /**
    * Force owner's delete version files by admin role.
    *
    * @param string $owner
    * @param string $file
    * @return bool
    */
    public static function deleteVersionFiles($owner,$file){
       //$file = 1.txt
       //$file = 2
       //$file = 3/1.txt
       $view = new \OC\Files\View("/$owner");

       if($view->is_dir('files_versions' . '/' . $file)){
           $files = $view->getDirectoryContent('files_versions' . '/' . $file);


           foreach($files as $fileArray) {
               $filename = $fileArray['name'];
               $filePath = $file . '/' . $filename;
                   self::deleteVersionFiles($owner,$view,$filePath);
           }
           $view->unlink('files_versions' . '/' . $file);
       }
       else{

           $versions = OCA\Files_Versions\Storage::getVersions($owner,$file);
           if (!empty($versions)) {
               foreach ($versions as $v) {
                   self::deletePreviewFiles($owner,$view,'files_versions/'. $file . ".v".$v['version']);
                   $view->unlink('files_versions/'. $file . ".v".$v['version']);
               }
           }
       }
    }
    /**
    * Force owner's delete preview files by admin role.
    *
    * @param string $owner
    * @param string $file
    * @return bool
    */
    public static function deletePreviewFiles($owner,$view,$file){
       //$file = files/1.txt
       //$file = files/2
       //$file = files/3/1.txt

       $dirs = explode("/", $file);
       $rootDir = $dirs[0];
       $filterFilePath = preg_replace("/^$rootDir\//",'',$file);

       if($view->is_dir($filterFilePath)){
           $files = $view->getDirectoryContent($filterFilePath);
           foreach($files as $fileArray) {
               $filename = $fileArray['name'];
               $filePath = $file . '/' . $filename;
               self::deletePreviewFiles($owner,$view,$filePath);
           }
       }
       else{
           $preview = new \OC\Preview($owner, $rootDir,preg_replace("/^$rootDir\//",'',$file));
           $preview->deleteAllPreviews();
       }

    }
}
