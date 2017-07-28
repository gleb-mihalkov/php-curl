<?php
namespace Curl
{
    use Curl\Base\Base;

    /**
     * Обработчик разделяемых ресурсов.
     */
    class Shared extends Base
    {
        /**
         * Куки.
         */
        const COOKIE = CURL_LOCK_DATA_COOKIE;

        /**
         * Кеш DNS. Обратите внимание, что если вы используете множественный обработчик cURL,
         * то разделены будут кеши всех обработчиков в его составе.
         */
        const DNS = CURL_LOCK_DATA_DNS;

        /**
         * Идентификаторы сессий SSL. Позволяет сократить время на установление сессии SSL при
         * переконнекте к тому же самому серверу. Помните, что идентификатор сессии SSL будет
         * по умолчанию переиспользоваться тем же самым обработчиком.
         */
        const SSL = CURL_LOCK_DATA_SSL_SESSION;

        /**
         * Создает соединение.
         * @return resource Соединение.
         */
        protected function init()
        {
            return curl_share_init();
        }

        /**
         * Задает настройки соединение.
         * @param  resource $connection Соединение.
         * @param  integer  $name       Код настройки.
         * @param  mixed    $value      Значение настройки.
         * @return void
         */
        protected function setopt($connection, $name, $value)
        {
            curl_share_setopt($connection, $name, $value);
        }

        /**
         * Закрывает соединение.
         * @param  resource $connection Соединение.
         * @return void
         */
        protected function close($connection)
        {
            curl_share_close($connection);
        }

        /**
         * Создает экземпляр класса.
         * @param integer|integer[] $resources Код или коды ресурсов, которые следует сделать
         *                                     разделяемыми.
         */
        public function __construct($resources = null)
        {
            parent::__construct();
            if ($resources == null) return;

            $this->share($resources);
        }

        /**
         * Задает массив настроек разделяемых данных.
         * @internal
         * @param  integer $name   Код настройки.
         * @param  array   $values Коды разделяемых данных.
         * @return void
         */
        protected function _share($name, $values)
        {
            if (!is_array($values))
            {
                $values = [$values];
            }

            $this->connect();

            foreach ($values as $value)
            {
                curl_share_setopt($this->connection, $name, $value);
            }
        }

        /**
         * Объявляет, какие данные будут общими для связаных обработчиков.
         * @param  integer|integer[] $resources Код или список кодов разделяемых ресурсов.
         * @return Shared                       Этот же объект.
         */
        public function share($resources)
        {
            $this->_share(CURLSHOPT_SHARE, $resources);
            return $this;
        }

        /**
         * Объявляет, какие ресурсы не будут общими для связанных обработчиков.
         * @param  integer|integer[] $resources Код или список кодов разделяемых ресурсов.
         * @return Shared                       Этот же объект.
         */
        public function unshare($resources)
        {
            $this->_share(CURLSHOPT_UNSHARE, $resources);
            return $this;
        }

        /**
         * Добавляет дочерние обработчики для данного.
         * @param  Curl|Curl[] $curls Обработчик или список обработчиков.
         * @return Shared             Этот же объект.
         */
        public function add($curls)
        {
            if (!is_array($curls))
            {
                $curls = [$curls];
            }

            $this->connect();

            foreach ($curls as $curl)
            {
                $curl->option(CURLOPT_SHARE, $this->connection);
            }

            return $this;
        }

        /**
         * Создает соединение, которое по умолчанию разделяет указанные в данном объекте ресурсы.
         * @param string|array|Curl $source Источник настроек для экземпляра - либо адрес ресурса,
         *                                  к которому следует выполнить подключение, либо
         *                                  массив с настройками соединения, либо другой экземпляр
         *                                  класса, чьи настройки должны быть унаследованы.
         * @return Curl Соединение.
         */
        public function create($source = null)
        {
            $curl = new Curl($source);
            $this->add($curl);

            return $curl;
        }
    }
}