<?php

namespace handle\room;

interface GameRoom
{
    const ROOM_TYPE_KINGS_FIVE_BY_FIVE = 'king_5_vs_5';

    const ROOM_TYPE_KINGS_ONE_BY_ONE = 'king_1_vs_1';

    const ROOM_TYPE_CHICKEN = 'chicken';

    public function getType();

    public function getRoomID();

    public function getPlayerCount();

    public function join($member);

    public function  leave($client_id);

    public function hasPlayer($client_id);

}