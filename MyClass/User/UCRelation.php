<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/4/14
 * Time: 20:27
 */

namespace MyClass\User;
use Illuminate\Support\Facades\DB;

class UCRelation
{
    /**
     * 添加
     * @param $user_id
     * @param $community_id
     * @return id/false
     */
    public static function addLink($user_id,$community_id)
    {
       $result = DB::table("user_community_group")->insertGetId(
           [
               "re_user" => $user_id,
               "re_community_group" => $community_id
           ]
       );
        return $result;

    }

    /**
     * 删除
     * @param $user_id
     * @param $community_id
     * @return bool
     */
    public static function delLink($user_id,$community_id)
    {
        $result = DB::table("user_community_group")
            ->where("re_user","=",$user_id)
            ->where("re_community","=",$community_id)
            ->delete();
        return $result;
    }

    /**
     * 判断用户是否在社区中
     * @param $user_id
     * @param $community_id
     * @return bool
     */
    public static function userInCommunity($user_id,$community_id)
    {
        $result = DB::table("user_community_group")
            ->where("re_user","=",$user_id)
            ->where("re_community_group","=",$community_id)
            ->get();
        if(! empty($result))
        {
            return true;
        }
        return false;
    }

}