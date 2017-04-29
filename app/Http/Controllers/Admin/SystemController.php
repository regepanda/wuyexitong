<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 16:26
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use MyClass\Base\GuiFunction;
use MyClass\System\Message;
use MyClass\User\User;
use Illuminate\Support\Facades\Request as SysRequest;
use MyClass\Admin\PowerGroup;
use MyClass\Exception\PowerException;

class SystemController extends Controller
{

    public function __construct(GuiFunction $guiFunc)
    {
        if(PowerGroup::checkAdminPower(5))
        {

        }
        else
        {
            throw new PowerException("你没有此操作权限！");
        }

    }


    //显示系统信息页面，加载angularJs
    public function sMessage(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sMessage");
        return view("Admin.System.sMessage");
    }

    //查询系统信息并分页、排序、条件查找
    public function allMessage()
    {
        $data = Message::select(SysRequest::all());
        return response()->json($data);
    }

    //系统信息详情
    public function messageDetail()
    {
            //接收传过来的message_id
            $messageDetailData = SysRequest::all();
            $message = new Message($messageDetailData['message_id']);
            return response()->json($message->info);
    }

    //删除系统信息
    public function messageDelete()
    {
            $messageDeleteData = SysRequest::all();
            $message = new Message($messageDeleteData['message_id']);
            if($message->delete())
            {
                return response()->json(["status"=>true,"message"=>"删除成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"删除失败"]);
            }
    }

    //获取所有的用户组
    public function getAllUserGroup()
    {
        $userGroup = User::getAllUserGroup();
        return response()->json($userGroup);
    }


    //向用户组发送系统消息
    public function sendMessageToGroup()
    {
        $sendMessageToGroupData = SysRequest::all();
        if(!empty($sendMessageToGroupData['group_id']) && !empty($sendMessageToGroupData['message_data']))
        {
            if(Message::sendMessageToUserGroup($sendMessageToGroupData['group_id'],$sendMessageToGroupData['message_data']))
            {
                return response()->json(["status"=>true,"message"=>"发送成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"发送失败"]);
            }
        }
        else
        {
            throw new PowerException("两表单字段都不能为空");
        }
    }
}
