<?php

namespace handle\message;

/**
 * message json {'what':'',obj:''}
 *
 * obj json {'form':'','to':'',content:''}
 *
 */
interface GameMessage
{
    const MESSAGE_WHAT_LOGIN = 'login';

    const MESSAGE_WHAT_LOGOUT = 'logout';

    const MESSAGE_WHAT_JOIN_ROOM = 'join_room';

    const MESSAGE_WHAT_LEAVE_ROOM = 'leave_room';

    const MESSAGE_WHAT_ROOM_READY = 'room_ready';

    const MESSAGE_WHAT_BROADCAST = 'broadcast';

    const MESSAGE_WHAT_ROOM_BROADCAST = 'room_broadcast';

    const MESSAGE_WHAT_ONE_TO_ONE = 'one_to_one';

    const MESSAGE_WHAT_RESPONSE = 'response';

    public function getWhat();

    public function getObj();

    public static function parse($message);
}