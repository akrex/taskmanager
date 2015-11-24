<?php
final class Registry {
    //класс для удобного доступа к глобальным переменным
    private $data = array();

    public function get($key) {
        return (isset($this->data[$key]) ? $this->data[$key] : NULL);
    }

    public function set($key, $value) {
        $this->data[$key] = $value;
    }

    public function has($key) {
        return isset($this->data[$key]);
    }
}
