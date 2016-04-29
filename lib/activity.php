<?php
namespace OCA\Files_Report;

use OCP\Activity\IExtension;
use OCP\Activity\IManager;
use OCP\IURLGenerator;
use OCP\L10N\IFactory;
use OCP\IL10N;
use OCA\Files_Report\Constants;

class Activity extends Constants implements IExtension {
    const SUBJECT_REPORTED_WITH = 'reported_with';
    const TYPE_REPORTED = 'reported';

    protected $languageFactory;
    protected $URLGenerator;
	protected $activityManager;
    
    public function __construct(IFactory $languageFactory, IURLGenerator $URLGenerator, IManager $activityManager) {
		$this->languageFactory = $languageFactory;
        $this->URLGenerator = $URLGenerator;
        $this->activityManager = $activityManager;
	}

    protected function getL10N($languageCode = null) {
		return $this->languageFactory->get('files_report', $languageCode);
	}

    public function getNotificationTypes($languageCode) {
        $l = $this->getL10N($languageCode);
        
        return [self::TYPE_REPORTED => $l->t('A file has been <strong>force deleted</strong>.')];
    }

    public function getDefaultTypes($method) {
       return [self::TYPE_REPORTED,];
       
    }

    public function getTypeIcon($type) {
        if($type === self::TYPE_REPORTED) { 
            return 'icon-delete-color';
        } else {
            return false;
        }
    }

    public function translate($app, $text, $params, $stripPath, $highlightParams, $languageCode) {
		if ($app !== 'files_report') {
			return false;
		}

		$l = $this->getL10N($languageCode);
        if($text === self::SUBJECT_REPORTED_WITH) {
            $params[1] = $l->t(self::$reason_arr[(int) $params[1]]);
            return (string) $l->t('Your file <strong>%1$s</strong> has been force deleted by administrator because this file is <strong>%2$s</strong>.',$params);

        }

	}

    public function getSpecialParameterList($app, $text) {
        return false;
    }

    public function getGroupParameter($activity) {
        return false;
    }

    public function isFilterValid($filterValue) {
        return false;
	}

    public function getNavigation() {
        return false;
    }

    public function filterNotificationTypes($types, $filter) {
		return false;

    }

    public function getQueryForFilter($filter) {
        return false;
    }


}

?>
