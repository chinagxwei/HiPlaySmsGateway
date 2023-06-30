<?php

namespace handle\room;

use GatewayWorker\Lib\Gateway;
use GuzzleHttp\Exception\GuzzleException;
use handle\config\Api;
use handle\member\Member;
use Workerman\Lib\Timer;

class BaseGameRoom implements GameRoom
{
    private $members = [];

    private $type;

    private $room_id;

    private $remote;

    /**
     * @param $type
     * @param $room_id
     */
    public function __construct($type, $room_id)
    {
        $this->type = $type;
        $this->room_id = $room_id;
//        $this->checkStartByType();
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
        return $this->type == self::ROOM_TYPE_KINGS_FIVE_BY_FIVE;
    }

    /**
     * @return bool
     */
    public function isKings1v1()
    {
        return $this->type == self::ROOM_TYPE_KINGS_ONE_BY_ONE;
    }

    /**
     * @return bool
     */
    public function isChicken()
    {
        return $this->type == self::ROOM_TYPE_CHICKEN;
    }

    /**
     * 加入房间
     *
     * @param Member $member
     * @return void
     * @throws GuzzleException
     */
    public function join($member)
    {
        $this->members[$member->getClientId()] = [
            'ready' => false,
            'member' => $member
        ];
        Gateway::joinGroup($member->getClientId(), $this->room_id);
        echo "[" . $member->getUserId() . "] join room!\n";
        $params = $this->getRoomInfo($member->getUserId());
        if (!empty($params['data'])) {

            // 推送人数
            $join_num = $params['data']['room']['join_num'];
            $qrcode_path = $params['data']['room']['QrCodePath'];
            $game_type = $params['data']['room']['game_type'];
            $res = \handle\message\BaseMessage::getRoomMessage(
                [
                    'join_num' => $join_num,
                    'qrcode_path' => $qrcode_path,
                    'game_type' => $game_type,
                    'join_type' => $params['data']['join_type'],
                    'sms_player_count' => $this->getPlayerCount()
                ]
            );
            Gateway::sendToGroup($this->room_id, json_encode($res));
        }
    }

    /**
     * 离开房间
     *
     * @param $client_id
     * @return void
     * @throws GuzzleException
     */
    public function leave($client_id)
    {
        $item = $this->members[$client_id];
        $item['member']->leaveRoom($this->room_id);
        echo "[" . $item['member']->getUserId() . "] leave room!\n";
        unset($this->members[$client_id]);
        Gateway::leaveGroup($client_id, $this->room_id);
//        $params = $this->getRoomInfo();
//        if (!empty($params['data'])) {
//            // 推送人数
//            $join_num = $params['data']['room']['join_num'];
//            $qrcode_path = $params['data']['room']['QrCodePath'];
//            $game_type = $params['data']['room']['game_type'];
//            $res = \handle\message\BaseMessage::getRoomMessage(
//                [
//                    'join_num' => $join_num,
//                    'qrcode_path' => $qrcode_path,
//                    'game_type' => $game_type
//                ]
//            );
//            Gateway::sendToGroup($this->room_id, json_encode($res));
//        }
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

//    /**
//     * @return void
//     */
//    private function checkStartByType()
//    {
//        switch ($this->type) {
//            case GameRoom::ROOM_TYPE_KINGS_FIVE_BY_FIVE:
//                $this->checkStart(10);
//                break;
//            case GameRoom::ROOM_TYPE_KINGS_ONE_BY_ONE:
//                $this->checkStart(2);
//                break;
//            case GameRoom::ROOM_TYPE_CHICKEN:
//                break;
//        }
//    }

//    /**
//     * 检查是否全部准备了
//     *
//     * @param $checkCount
//     * @return void
//     */
//    private function checkStart($checkCount)
//    {
//        if ($this->getPlayerCount() >= $checkCount) {
//            if ($this->timer !== null) {
//                Timer::del($this->timer);
//                $this->timer = null;
//            }
//            $this->timer = Timer::add(3, function () {
//                $count = 0;
//                foreach ($this->members as $member) {
//                    if (!$member['ready']) {
//                        $count = $count + 1;
//                        // 推送人数
////                                Gateway::sendToClient($member['member']->getClientId(), "");
//                    }
//                }
//                if ($count === 0) {
//                    Timer::del($this->timer);
//                    $this->timer = null;
//                }
//            }, null, false);
//        }
//    }

    public function getPlayerCount()
    {
        // TODO: Implement getPlayerCount() method.
        return count($this->members);
    }

    /**
     * @return mixed
     * @throws GuzzleException
     */
    private function getRoomInfo($member_id)
    {
        $http = \handle\util\HttpReprint::getInstance();
        $res = $http->post(Api::MATCH_DETAIL, ['room_id' => $this->room_id]);
        $params = json_decode($res, JSON_UNESCAPED_UNICODE);
        $join_type = -1;
        if (!empty($params['data']) && !empty($params['data']['room']['user_participate_game_list'])) {
            $user_participate_game_list = $params['data']['room']['user_participate_game_list'];
            foreach ($user_participate_game_list as $key => $value) {
                foreach ($value as $item) {
                    if ($item['id'] == $member_id) {
                        if ($key === "teamblue") {
                            $join_type = 1;
                        } else {
                            $join_type = 2;
                        }
                        break;
                    }
                }
            }
        }
        $params['data']['join_type'] = $join_type;
        return $params;
    }
}