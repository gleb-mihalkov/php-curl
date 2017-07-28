<?php
namespace Curl
{
    use Curl\Base\Base;

    /**
     * Обработчик множественных запросов.
     */
    class Multi extends Base
    {
        /**
         * Создает обработчик.
         * @return resource Соединение.
         */
        protected function init()
        {
            return curl_multi_init();
        }

        /**
         * Задает параметр настройки соединения.
         * @param  resource $connection Соединение.
         * @param  integer  $name       Код настройки.
         * @param  mixed    $value      Значение настройки.
         * @return void
         */
        protected function setopt($connection, $name, $value)
        {
            curl_multi_setopt($connection, $name, $value);
        }

        /**
         * Закрывает соединение.
         * @param  resource $connection Соединение.
         * @return void
         */
        protected function close($connection)
        {
            curl_multi_close($connection);
        }
    }
}