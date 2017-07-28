<?php
namespace Curl
{
    use Curl\Base\Exec;

    /**
     * Обработчик CURL.
     */
    class Curl extends Exec
    {
        /**
         * Создает соединение.
         * @return resource Соединение.
         */
        protected function init()
        {
            return curl_init();
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
            curl_setopt($connection, $name, $value);
        }

        /**
         * Выполняет запрос.
         * @param  resource $connection Соединение.
         * @return mixed                Результат запроса.
         */
        protected function execute($connection)
        {
            return curl_exec($connection);
        }

        /**
         * Закрывает соединение.
         * @param  resource $connection Соединение.
         * @return void
         */
        protected function close($connection)
        {
            curl_close($connection);
        }

        /**
         * Создает экземпляр класса.
         * @param string|array|Curl $source Источник настроек для экземпляра - либо адрес ресурса,
         *                                  к которому следует выполнить подключение, либо
         *                                  массив с настройками соединения, либо другой экземпляр
         *                                  класса, чьи настройки должны быть унаследованы.
         */
        public function __construct($source = null)
        {
            if ($source && is_string($source))
            {
                $source = [CURLOPT_URL => $source];
            }

            parent::__construct($source);
        }

        /**
         * Выполняет запрос, при необходимости задавая дополнительные настройки конкретно для
         * этого запроса.
         * @param  string|array|Request $data Либо адрес, на который следует сделать запрос,
         *                             либо массив с дополнительными настройками, либо
         *                             другой объект Curl, чьи настройки нужно унаследовать.
         * @return mixed               Результат запроса.
         */
        public function exec($data = null)
        {
            if ($data && is_string($data))
            {
                $data = [CURLOPT_URL => $data];
            }

            return parent::exec($data);
        }

        /**
         * Возвращает информацию о последнем запросе.
         * @param  integer $name Код параметра.
         * @return mixed         Значение параметра или весь массив с информацией.
         */
        public function getInfo($name = null)
        {
            if ($this->connection == null) return null;

            return $name != null
                ? curl_getinfo($this->connection, $name)
                : curl_getinfo($this->connection);
        }

        /**
         * Останавливает или возобновляет запрос соединение.
         * @param  integer $state Код остановки или восстановления.
         * @return void
         */
        public function pause($state)
        {
            if ($this->connection == null) return;

            $result = curl_pause($this->connection, $state);
            if ($result == CURLE_OK) return;

            throw new Exception($result);
        }
    }
}