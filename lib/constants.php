<?php

namespace OCA\Files_Report;


class Constants {
    const SPAM_FILE = 1;
    const PORM_PHOTO_OR_FILE = 2;
    const CONTAINS_BAD_WORDS = 3;

    const PENDING_STATE = 0;
    const REPORT_STATE = 1;
    const CANCEL_STATE = 2;

    protected static $reason_arr = [
        0 => 'include bad words or graphs',
        1 => 'uncomfortable file',
        2 => 'should not be on custom cloud',
        3 => 'spam file',
        4 => 'not illegal file'

    ];
}





?>
