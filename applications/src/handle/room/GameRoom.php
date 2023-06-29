<?php

namespace handle\room;

interface GameRoom
{
    const ROOM_TYPE_KINGS_FIVE_BY_FIVE = 1;

    const ROOM_TYPE_KINGS_ONE_BY_ONE = 58;

    const ROOM_TYPE_CHICKEN = 2;

    public function getType();

    public function getRoomID();

    public function getPlayerCount();

    public function join($member);

    public function  leave($client_id);

    public function hasPlayer($client_id);

}