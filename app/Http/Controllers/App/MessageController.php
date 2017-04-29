<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/4/10
 * Time: 10:32
 */

namespace app\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use MyClass\System\Message;

class MessageController extends Controller
{

    //获取当前用户的消息
    public function getMessage()
    {
        /*
         * 符合标准查询
            返回标准结构，data里面有数据
            [
                {
                    message_read,是否已读
                    message_data,数据
                    message_create_time 创建时间
                },
            ]
         */
        $queryLimit = Request::all();
        if(isset($queryLimit['access_token']))
        {
            unset($queryLimit["access_token"]);
        }
        //开始查询
        if($messageData = Message::select($queryLimit))
        {
            //移除一些不需要的字段
            $s = count($messageData["data"]);
            for($i = 0 ;$i < $s;$i ++)
            {
                $data[$i]["message_read"] = $messageData["data"][$i]->message_read;
                $data[$i]["message_data"] = $messageData["data"][$i]->message_data;
                $data[$i]["message_create_time"] = $messageData["data"][$i]->message_create_time;
            }
            return response()->json(["status" => true, "message" => "正确", "data" => $data, "result_code" => "0"]);
        }
        else
        {
            //查询失败返回码
            $messageData['result_code'] = -1;
            $messageData['message'] = "查询失败";
            $messageData['status'] = false;
            $messageData['data'] = null;
            return response()->json($messageData);
        }
    }
    //设定一条消息为已读
    public function setReadMessage()
    {
        /*
         * 传递参数
            |-message_id  消息id

            返回标准结构，data为空
         */
        $postData = Request::all();
        $message = new Message($postData['message_id']);
        if($message->setReadMessage())
        {
            return response()->json(["status" => true, "message" => "正确", "data" =>[], "result_code" => "0"]);
        }
        else
        {
            return response()->json(["status" => true, "message" => "错误", "data" => [], "result_code" => "-1"]);
        }
    }
}