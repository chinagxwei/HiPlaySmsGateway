<?php

namespace handle\message;

class SmsMessage extends BaseMessage implements GameMessageObj
{
    protected $from;

    protected $to;

    protected $content;

    /**
     * @param $message
     */
    public function __construct($message)
    {
        $this->what = $message['what'];
        $this->obj = $message['obj'];
        $this->from = empty($message['obj']['from']) ? null : $message['obj']['from'];
        $this->to = empty($message['obj']['to']) ? null : $message['obj']['to'];
        $this->content = empty($message['obj']['content']) ? null : $message['obj']['content'];
    }


    public function getFrom()
    {
        // TODO: Implement getFrom() method.
        if (empty($this->from) && !empty($this->obj['form'])) {
            $this->from = $this->obj['form'];
        }
        return $this->from;
    }

    public function getTo()
    {
        // TODO: Implement getTo() method.

        if (empty($this->to) && !empty($this->obj['to'])) {
            $this->to = $this->obj['to'];
        }
        return $this->to;
    }

    public function getContent()
    {
        // TODO: Implement getContent() method.

        if (empty($this->content) && !empty($this->obj['content'])) {
            $this->content = $this->obj['content'];
        }
        return $this->content;
    }

    public static function parse($message)
    {
        // TODO: Implement parse() method.
        $data = json_decode($message, JSON_UNESCAPED_UNICODE);
        return new SmsMessage($data);
    }
}