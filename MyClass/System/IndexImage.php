<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/29
 * Time: 10:30
 */

namespace MyClass\System;
use Illuminate\Support\Facades\DB;
use MyClass\System\SystemMap;
use MyClass\System\Image;

class IndexImage
{
    /*APP端
     * 查询所有的首页图片
     */
    public static function appSelect()
    {
        $imageIds = array();
        $key = "index_images";
        $imageData = SystemMap::index($key);
        $imageData = json_decode($imageData,true);
        return $imageData;
    }

    /*
     * 查询所有的首页图片
     */
    public static function select()
    {
        $imageIds = array();
        $key = "index_images";
        $imageData = SystemMap::index($key);
        $imageData = json_decode($imageData,true);
        if($imageData == null) {return [];}
        foreach($imageData as $key => $value)
        {
            $imageIds[] = $value['imageId'];
        }

        return $imageIds;
    }

    //添加一张首页图片
    public static function add($image_id,$image_url)
    {
        //查看一下传过来的image_id在system_map表中的system_value中是否存在，存在就添加
        $key = "index_images";
        $system_value = SystemMap::index($key);
        if($system_value != null)
        {
            $system_value = json_decode($system_value);
            //看看有没有
            foreach($system_value as $value)
            {
                if($value->imageId == $image_id)
                {
                    return false;
                }
            }
            $systemValue['imageId'] = $image_id;
            $systemValue['link'] = $image_url;
            $system_value[] = $systemValue;
            $system_value = json_encode($system_value);
            if(SystemMap::index($key,$system_value))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $value['imageId'] = $image_id;
            $value['link'] = $image_url;
            $system_value[] = $value;
            $system_value = json_encode($system_value);
            if(SystemMap::index($key,$system_value))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    //删除一张首页图片
    public static function delete($image_id)
    {
        $system_key = "index_images";
        $system_value = SystemMap::index($system_key);
        $system_value = json_decode($system_value);
        foreach($system_value as $key => $value)
        {
            if((int)$image_id == (int)$value->imageId)
            {
                unset($system_value[$key]);
            }
        }
        $systemValue = [];
        foreach($system_value as $key => $value)
        {
            $systemValue[] = $system_value[$key];
        }
        $systemValue = json_encode($systemValue);

        if(SystemMap::index($system_key,$systemValue))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}