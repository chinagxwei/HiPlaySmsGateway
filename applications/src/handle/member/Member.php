<?php

namespace handle\member;

use GatewayWorker\Lib\Gateway;
use handle\ChatRoomLobby;

class Member
{
    private $user_id;

    private $client_id;

    private $nickname;

    private $mobile;

    private $vip;

    private $token;

    private $rooms = [];

    private $online = true;

    /**
     * @param $client_id
     * @param $param
     */
    public function __construct($client_id, $params)
    {
        $this->client_id = $client_id;
        $this->user_id = $params['id'];
        $this->nickname = $params['nick_name'];
        $this->mobile = $params['mobile'];
        $this->vip = $params['vip'];
        $this->token = $params['token'];
    }

    public function isOnline(){
        return $this->online;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @param mixed $client_id
     */
    public function setClientId($client_id)
    {
        $this->client_id = $client_id;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return array
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    public function addRoom($room_id)
    {
        $this->rooms[$room_id] = $room_id;
    }

    public function leaveRoom($room_id)
    {
        unset($this->rooms[$room_id]);
    }

    public function sendMessage($message)
    {
        if (is_array($message)) {
            $output = json_encode($message);
        } else {
            $output = trim($message);
        }

        Gateway::sendToClient($this->client_id, $output);
    }

    public function logout()
    {
        $hall = ChatRoomLobby::getInstance();
        while ($room_id = array_pop($this->rooms)) {
            if ($room_id > 0) {
                $room = $hall->getRoom($room_id);
                $room->leave($this->client_id);
            }
        }
        $this->online = false;
    }

    public function __serialize()
    {
        // TODO: Implement __serialize() method.
        return [
            'user_id' => $this->user_id,
            'client_id' => $this->client_id,
            'token' => $this->token,
            'rooms' => $this->rooms,
        ];
    }

    public function __unserialize(array $data)
    {
        // TODO: Implement __unserialize() method.
        $this->client_id = empty($data['client_id']) ? 0 : $data['client_id'];
        $this->user_id = empty($data['user_id']) ? 0 : $data['user_id'];
        $this->token = empty($data['token']) ? 0 : $data['token'];
        $this->rooms = empty($data['rooms']) ? 0 : $data['rooms'];
    }
}