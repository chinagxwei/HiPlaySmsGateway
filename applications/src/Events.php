<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */

//declare(ticks=1);


use GatewayWorker\Lib\Gateway;
use handle\ChatRoomLobby;
use handle\config\Api;
use Workerman\Lib\Timer;


/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{

    public static function onWebSocketConnect($client_id, $data)
    {

        $hall = ChatRoomLobby::getInstance();
        if (empty($hall->getMemberByClient($client_id))) {
            $_SESSION['auth_timer_id'] = Timer::add(5, function ($client_id) {
                $res = \handle\message\BaseMessage::getResponseMessage("login timeout");
                Gateway::sendToClient($client_id, json_encode($res));
                Gateway::closeClient($client_id);
            }, array($client_id), false);
        }
    }

    /**
     * 当客户端发来消息时触发
     *
     * 消息处理
     * 判断是否登录-》添加到聊天室大厅-》判断消息类型-》根据消息内容进行处理（如加入房间、退出房间、发送房间消息）
     *
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function onMessage($client_id, $message)
    {
        $hall = ChatRoomLobby::getInstance();

        $actionMessage = \handle\message\ActionMessage::parse($message);

        if ($actionMessage->isLogin()) {
            $member = $hall->getMemberByClient($client_id);
            if (empty($member) || !$member->isOnline()) {
                $http = \handle\util\HttpReprint::getInstance($actionMessage->getObj());
                $res = $http->post(Api::CHECK_LOGIN);
                $params = json_decode($res, JSON_UNESCAPED_UNICODE);
                if (!empty($member) && (!$member->isOnline() === false)) {
                    $member->setClientId($client_id);
                } else {
                    $hall->addMember(new \handle\member\Member($client_id, $params['data']));
                }
                $res = \handle\message\BaseMessage::getResponseMessage("login success");
                Timer::del($_SESSION['auth_timer_id']);
                Gateway::bindUid($client_id, $member->getUserId());
                Gateway::sendToClient($client_id, json_encode($res));
            } else {
                $res = \handle\message\BaseMessage::getResponseMessage("login error");
                Gateway::sendToClient($client_id, json_encode($res));
                Gateway::closeClient($client_id);
            }
        } else {
            if ($member = $hall->getMemberByClient($client_id)) {

                switch ($actionMessage->getWhat()) {
                    case \handle\message\BaseMessage::MESSAGE_WHAT_LOGOUT:
                        $member->logout();
                        break;
                    case \handle\message\BaseMessage::MESSAGE_WHAT_JOIN_ROOM:
                        if (is_numeric($actionMessage->getObj())) {
                            $hall->getRoom((int)$actionMessage->getObj())->join($member);
                            $res = \handle\message\BaseMessage::getResponseMessage("join success");
                        } else {
                            $res = \handle\message\BaseMessage::getResponseMessage("room ID error");
                        }
                        Gateway::sendToClient($client_id, json_encode($res));
                        break;
                    case \handle\message\BaseMessage::MESSAGE_WHAT_LEAVE_ROOM:
                        if (is_numeric($actionMessage->getObj())) {
                            $hall->getRoom((int)$actionMessage->getObj())->leave($member->getClientId());
                            $res = \handle\message\BaseMessage::getResponseMessage("leave success");
                        } else {
                            $res = \handle\message\BaseMessage::getResponseMessage("room ID error");
                        }
                        Gateway::sendToClient($client_id, json_encode($res));
                        break;
                    case \handle\message\BaseMessage::MESSAGE_WHAT_ROOM_READY:
                        if (is_numeric($actionMessage->getObj())) {
                            $hall->getRoom((int)$actionMessage->getObj())->ready($member->getClientId());
                        }
                        break;
//                case \handle\message\BaseMessage::MESSAGE_WHAT_ROOM_BROADCAST:
//                    break;
//                case \handle\message\BaseMessage::MESSAGE_WHAT_BROADCAST:
//                    break;
//                case \handle\message\BaseMessage::MESSAGE_WHAT_ONE_TO_ONE:
//                    break;
                }
            }
        }
    }

    /**
     * 当用户断开连接时触发
     *
     * 根据clineID获取会员对象，查看加入哪些房间，并进行退出
     *
     * @param int $client_id 连接id
     */
    public static function onClose($client_id)
    {
        $hall = ChatRoomLobby::getInstance();

        if ($member = $hall->getMemberByClient($client_id)) {
            $member->logout();
        }

        echo "[$client_id] client close connect!\n";
    }
}
