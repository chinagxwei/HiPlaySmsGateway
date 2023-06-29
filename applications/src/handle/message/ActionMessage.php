<?php

namespace handle\message;

class ActionMessage extends BaseMessage
{

    public function isLogin()
    {
        return $this->what === self::MESSAGE_WHAT_LOGIN;
    }

    public function isLogout()
    {
        return $this->what === self::MESSAGE_WHAT_LOGOUT;
    }

    public function isJoinRoom()
    {
        return $this->what === self::MESSAGE_WHAT_JOIN_ROOM;
    }

    public function isLeaveRoom()
    {
        return $this->what === self::MESSAGE_WHAT_LEAVE_ROOM;
    }

    public function isRoomBroadcast()
    {
        return $this->what === self::MESSAGE_WHAT_ROOM_BROADCAST;
    }

    public static function parse($message)
    {
        // TODO: Implement parse() method.
        $data = json_decode($message, JSON_UNESCAPED_UNICODE);
        return new ActionMessage($data);
    }
}