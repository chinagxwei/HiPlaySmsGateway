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

    public static function getResponseMessage($obj)
    {
        return [
            'what' => self::MESSAGE_WHAT_RESPONSE,
            'obj' => $obj
        ];
    }

    public static function getRoomMessage($obj)
    {
        return [
            'what' => self::MESSAGE_WHAT_ROOM_BROADCAST,
            'obj' => $obj
        ];
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