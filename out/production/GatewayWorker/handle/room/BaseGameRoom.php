<?php

namespace handle\room;

use GatewayWorker\Lib\Gateway;
use handle\member\Member;
use Workerman\Lib\Timer;

class BaseGameRoom implements GameRoom
{
    private $members = [];

    private $type = '';

    private $room_id;

    private $timer;

    /**
     * @param string $type
     * @param $room_id
     */
    public function __construct($type, $room_id)
    {
        $this->type = $type;
        $this->room_id = $room_id;
        $this->checkStartByType();
    }

    public function getType()
    {
        // TODO: Implement getType() method.
        return $this->type;
    }

    public function getRoomID()
    {
        // TODO: Implement getRoomID() method.
        return $this->room_id;
    }

    /**
     * @param $client_id
     * @return bool
     */
    public function hasPlayer($client_id)
    {
        return !empty($this->members[$client_id]);
    }

    /**
     * @return bool
     */
    public function isKings5v5()
    {
        return $this->type === self::ROOM_TYPE_KINGS_FIVE_BY_FIVE;
    }

    /**
     * @return bool
     */
    public function isKings1v1()
    {
        return $this->type === self::ROOM_TYPE_KINGS_ONE_BY_ONE;
    }

    /**
     * @return bool
     */
    public function isChicken()
    {
        return $this->type === self::ROOM_TYPE_CHICKEN;
    }

    /**
     * 加入房间
     *
     * @param Member $member
     * @return void
     */
    public function join($member)
    {
        $this->members[$member->getClientId()] = [
            'ready' => false,
            'member' => $member
        ];
        Gateway::joinGroup($member->getClientId(), $this->room_id);

        // 推送人数
//        Gateway::sendToGroup($this->room_id, "");
    }

    /**
     * 离开房间
     *
     * @param $client_id
     * @return void
     */
    public function leave($client_id)
    {
        $this->members[$client_id]['member']->leaveRoom($this->room_id);
        unset($this->members[$client_id]);
        Gateway::leaveGroup($client_id, $this->room_id);

        // 推送人数
//        Gateway::sendToGroup($this->room_id, "");
    }

    /**
     * 准备
     *
     * @param $client_id
     * @return void
     */
    public function ready($client_id)
    {
        $this->members[$client_id]['ready'] = true;
    }

    private function checkStartByType()
    {
        switch ($this->type) {
            case GameRoom::ROOM_TYPE_KINGS_FIVE_BY_FIVE:
                $this->checkStart(10);
                break;
            case GameRoom::ROOM_TYPE_KINGS_ONE_BY_ONE:
                $this->checkStart(2);
                break;
            case GameRoom::ROOM_TYPE_CHICKEN:
                break;
        }
    }

    /**
     * 检查是否全部准备了
     *
     * @param $checkCount
     * @return void
     */
    private function checkStart($checkCount)
    {
        if ($this->getPlayerCount() >= $checkCount) {
            if ($this->timer !== null) {
                Timer::del($this->timer);
                $this->timer = null;
            }
            $this->timer = Timer::add(3, function () {
                $count = 0;
                foreach ($this->members as $member) {
                    if (!$member['ready']) {
                        $count = $count + 1;
                        // 推送人数
//                                Gateway::sendToClient($member['member']->getClientId(), "");
                    }
                }
                if ($count === 0) {
                    Timer::del($this->timer);
                    $this->timer = null;
                }
            }, null, false);
        }
    }

    public function getPlayerCount()
    {
        // TODO: Implement getPlayerCount() method.
        return count($this->members);
    }
}