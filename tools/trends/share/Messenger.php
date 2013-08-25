<?php
class Messenger {

    private static function timeStamp() {
        return "[" . date(DATE_FORMAT) . "]";
    }

    private static function message($type, $message) {
        echo(Messenger::timeStamp() . $type . $message . ".\n");
    }

    public static function error($message) {
        Messenger::message(' ERROR: ', $message);
    }

    public static function log($message) {
        Messenger::message(' LOG: ', $message);
    }    
}
?>
