<?php

namespace handle\message;

 abstract class BaseMessage implements GameMessage
{
    protected $what = '';

    protected $obj = [];

    /**
     * @param string $what
     * @param array $obj
     */
    public function __construct($message)
    {
        $this->what = $message['what'];
        $this->obj = $message['obj'];
    }


    public function getWhat()
    {
        // TODO: Implement getWhat() method.
        return $this->what;
    }

    public function getObj()
    {
        // TODO: Implement getObj() method.
        return $this->obj;
    }

    public function __serialize()
    {
        // TODO: Implement __serialize() method.
        return [
            'what' => $this->getWhat(),
            'obj' => $this->getObj(),
        ];
    }

    public function __unserialize(array $data)
    {
        // TODO: Implement __unserialize() method.
        $this->what = $data['what'];
        $this->obj = $data['obj'];
    }
 }