<?php
class fileCache
{
        private static $path="/var/www/lib/cache/";
        public static function get($key){
                $val=json_decode(@file_get_contents(self::$path.$key),1);
                if(!$val || !isset($val['data'])) return null;
                if(isset($val['ttl']) && intval($val['ttl'])<time()) return null;
                return $val['data'];
        }
        public static function set($key, $val, $expires=0){ //$expires = number of minutes to lie
                $cache=array();
                $cache['data']=$val;
                if($expires){
                        $jitter=rand(0,4); //to prevent dog-pile
                        $cache['ttl']=$expires*60+time()+$jitter;
                }
                return file_put_contents(self::$path.$key, json_encode($cache));
        }
}

