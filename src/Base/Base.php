<?php
namespace Curl\Base
{
    use Curl\Exception;
    use ArrayAccess;

    /**
     * Базовый класс для любой сущности CURL.
     */
    abstract class Base implements ArrayAccess
    {
        /**
         * Создает дескриптор соединения нужного типа.
         * @return resource Дескриптор соединения.
         */
        abstract protected function init();

        /**
         * Задает массив настроек для соединения.
         * @param  resource $connection Дескриптор открытого соединения.
         * @param  integer  $name       Код настройки.
         * @param  mixed    $value      Значение настройки.
         * @return void
         */
        abstract protected function setopt($connection, $name, $value);

        /**
         * Закрывает дескриптор соединения.
         * @param  resource $connection Дескриптор соединения.
         * @return void
         */
        abstract protected function close($connection);

        /**
         * Дескриптор соединения.
         * @var resource
         */
        protected $connection;

        /**
         * Коллекция настроек запроса.
         * @var array
         */
        public $options = [];

        /**
         * Создает экземпляр класса.
         * @param string|array|Request $source Либо адрес, для которого создается запрос, либо
         *                                     массив предустановленных настроек запроса,
         *                                     либо другой объект Request, от которого нужно
         *                                     унаследовать настройки.
         */
        public function __construct($source = null)
        {
            if ($source == null) return;

            $this->option($source);
        }

        /**
         * Запрещает клонировать ссылку на открытое соединение.
         * @return void
         */
        public function __clone()
        {
            $this->connection = null;
        }

        /**
         * Закрывает открытое соединения после уничтожения объекта.
         */
        public function __destruct()
        {
            $this->disconnect();
        }

        /**
         * Получает дескриптор открытого соединения с установленными настройками.
         * @return resource Дескриптор открытого соединения.
         */
        public function getResource()
        {
            $this->connect();
            return $this->connection;
        }

        /**
         * Создает новое соединение с текущими настройками.
         * @return resource Дескриптор соединения.
         */
        protected function connect()
        {
            $this->connection = $this->connection ?: $this->init();
            
            foreach ($this->options as $name => $value)
            {
                $this->setopt($this->connection, $name, $value);
            }
        }

        /**
         * Закрывает открытое соединение.
         * @return void
         */
        public function disconnect()
        {
            if ($this->connection == null) return;
            $this->close($this->connection);
        }

        /**
         * Задает параметр настройки запроса.
         * @param  integer $name  Код параметра настройки.
         * @param  mixed   $value Значение параметра.
         * @return void
         */
        protected function setOption($name, $value)
        {
            $this->options[$name] = $value;
        }

        /**
         * Получает значение параметра настройки или null, если параметр не задан.
         * @param  integer $name Код параметра настройки.
         * @return mixed         Значение параметра или null.
         */
        protected function getOption($name)
        {
            return isset($this->options[$name])
                ? $this->options[$name]
                : null;
        }

        /**
         * Удаляет параметр настройки запроса.
         * @param  integer $name Код параметра настройки запроса.
         * @return void
         */
        protected function unsetOption($name)
        {
            unset($this->options[$name]);
        }

        /**
         * Получает или задает параметр запроса.
         * @param  integer|array $name  Код параметра или массив параметров.
         * @param  mixed         $value Значение параметра.
         * @return mixed                Текущее значение параметра.
         */
        public function option($name = null, $value = null)
        {
            if ($name == null)
            {
                return $this->options;
            }

            if ($value == null)
            {
                if ($name instanceof Base)
                {
                    $source = $name;

                    $this->options = array_merge($this->options, $source->options);
                    return $this;
                }

                if (is_array($name))
                {
                    $source = $name;

                    foreach ($source as $name => $value)
                    {
                        $this->setOption($name, $value);
                    }

                    return $this;
                }

                $value = $this->getOption($name);
                return $value;
            }

            $this->setOption($name, $value);
            return $this;
        }

        /**
         * Проверяет, существует ли параметр среди настроек запроса.
         * @param  integer $name Код параметра.
         * @return boolean       True или false.
         */
        public function offsetExists($name)
        {
            $value = $this->getOption($name);
            return $value !== null;
        }

        /**
         * Получает значение параметра настройки запроса.
         * @param  integer $name Код параметра.
         * @return mixed         Значение параметра.
         */
        public function offsetGet($name)
        {
            $value = $this->getOption($name);
            return $value;
        }

        /**
         * Задает значение параметра настройки запроса.
         * @param  integer $name  Код параметра.
         * @param  mixed   $value Значение параметра.
         * @return void
         */
        public function offsetSet($name, $value)
        {
            $this->setOption($name, $value);
        }

        /**
         * Удаляет значение параметра настройки запроса.
         * @param  integer $name Код параметра.
         * @return void
         */
        public function offsetUnset($name)
        {
            $this->unsetOption($name);
        }
    }
}