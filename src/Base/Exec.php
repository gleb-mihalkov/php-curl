<?php
namespace Curl\Base
{
    /**
     * Добавляет классу функционал отправки запроса.
     */
    abstract class Exec extends Base
    {
        /**
         * Выполняет запрос по установленному соединению.
         * @param  resource $connection Дескриптор соединения.
         * @return mixed                Результат запроса.
         */
        abstract protected function execute($connection);

        /**
         * Выполняет запрос, при необходимости задавая дополнительные настройки конкретно для
         * этого запроса.
         * @param  array|Request $data Либо адрес, на который следует сделать запрос,
         *                             либо массив с дополнительными настройками, либо
         *                             другой объект Curl, чьи настройки нужно унаследовать.
         * @return mixed               Результат запроса.
         */
        public function exec($data = null)
        {
            $isExtend = $data != null;

            if ($isExtend)
            {
                $options = $this->options;
                $this->option($data);
            }

            $this->connect();
            $response = $this->execute($this->connection);

            if ($isExtend)
            {
                $this->options = $options;
            }

            if ($response === false)
            {
                throw new Exception($this->connection);
            }

            return $response;
        }
    }
}