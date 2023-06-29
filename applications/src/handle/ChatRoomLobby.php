<?php

namespace handle;

use GuzzleHttp\Exception\GuzzleException;
use handle\config\Api;
use handle\member\Member;
use handle\room\BaseGameRoom;
use handle\room\GameRoom;

/**
 * 聊天室大厅
 */
class ChatRoomLobby
{
    private static $instance;

    /**
     * @var Member[]
     */
    private $members = [];

    private $mapping = [];

    /**
     * @var BaseGameRoom[]
     */
    private $rooms = [];

    private function __construct()
    {
    }

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param $client_id
     * @return Member|null
     */
    public function getMemberByClient($client_id)
    {
        if ($item = $this->members[$client_id]) {
            return $item;
        }
        return null;
    }

    /**
     * @param $user_id
     * @return Member|null
     */
    public function getMemberByUser($user_id)
    {
        if ($item = $this->members[$this->mapping[$user_id]]) {
            return $item;
        }
        return null;
    }

    /**
     * @param Member $member
     * @return void
     */
    public function addMember($member)
    {
        if (empty($this->mapping[$member->getUserId()])) {
            $this->members[$member->getClientId()] = $member;
            $this->mapping[$member->getUserId()] = $member->getClientId();
        } else {
            $item = $this->members[$this->mapping[$member->getUserId()]];
            $item->setClientId($member->getClientId());
        }
    }

    /**
     * @param $client_id
     * @return false|Member
     */
    public function removeMember($client_id)
    {
        if ($item = $this->members[$client_id]) {
            unset($this->mapping[$item->getUserId()]);
            unset($this->members[$client_id]);
            return $item;
        }
        return false;
    }


    /**
     * @param $room_id
     * @return BaseGameRoom
     * @throws GuzzleException
     */
    public function getRoom($room_id)
    {
        if (empty($this->rooms[$room_id])) {
            $http = \handle\util\HttpReprint::getInstance();
            $res = $http->post(Api::MATCH_DETAIL, ['room_id' => $room_id]);
            $params = json_decode($res, JSON_UNESCAPED_UNICODE);
            if (!empty($params['data'])){
                $this->rooms[$room_id] = new BaseGameRoom($params['data']['game_type'], $room_id);
            }
        }
        return $this->rooms[$room_id];
    }

    /**
     * @param BaseGameRoom $room
     * @return void
     */
    public function addRoom($room)
    {
        $this->rooms[$room->getRoomID()] = $room;
    }

    /**
     * @param $room_id
     * @return false|BaseGameRoom
     */
    public function removeRoom($room_id)
    {
        if ($item = $this->rooms[$room_id]) {
            unset($this->rooms[$room_id]);
            return $item;
        }
        return false;
    }
}