<?

class dbConnection
{
	protected static $c=null;	

	/*	
	 * @return PDO
	 */
	public static function get($db='xo')
	{
		if(!$db) $db='xo';
		if(!self::$c){
			$uri="mysql:host=restore2.c2teljdmlsvx.us-east-1.rds.amazonaws.com;port=3306;dbname=$db";
			self::$c=new PDO($uri,"ragnus","1cal2008");
		}else{
		}
		return self::$c;
	}
}
