<?php
namespace Curl
{
    use Exception as Base;

    /**
     * Исключение, возникшее при выполнении запроса.
     */
    class Exception extends Base
    {
        /**
         * Создает экземпляр класса.
         * @param resource $connection Дескриптор соединения.
         */
        public function __construct($connection)
        {
            $message = null;
            $code = null;
            
            if (is_resource($connection))
            {
                $message = curl_error($connection);
                $code = curl_errno($connection);
            }
            else
            {
                $code = $connection;
                $message = curl_strerror($code);
            }
            
            parent::__construct($message, $code);
        }
    }
}