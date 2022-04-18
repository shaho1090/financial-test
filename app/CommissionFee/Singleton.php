<?php


namespace App\CommissionFee;


class Singleton
{
    private static array $instances = [];

    protected function __construct() { }

    protected function __clone() { }

    /**
     * @throws \Exception
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    /**
     * The method you use to get the Singleton's instance.
     */
    public static function getInstance(?Object $class=null)
    {
        $subclass = static::class;

        if (!isset(self::$instances[$subclass])) {

            self::$instances[$subclass] = new static($class);
        }

        return self::$instances[$subclass];
    }

    public function __destruct(){
        // unset($this->_connection);
        self::$instances =[];
    }
}
