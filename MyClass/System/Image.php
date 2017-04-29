<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 8:39
 */

namespace MyClass\System;

use Illuminate\Support\Facades\DB;
use Validator;
use MyClass\Exception\SysValidatorException;
/**
 * Class Image
 * @package BaseClass\Component\Image
 */
class Image
{
    /**
     * 图片id
     * @var
     */
    private $image_id;
    /**
     * @param $image_id
     */
    public $info;

    public function __construct($image_id)
    {
        $this->image_id = $image_id;
        $this->getInfo();
    }

    //获取一条记录
    public function getInfo()
    {
        $this->info = DB::table("image")->where("image_id", "=", $this->image_id)->first();
        if ($this->info != null) {
            return $this->info;
        } else {
            return false;
        }
    }



    /*
     * 传入数组，进行条件查询，下面是参数为数组的各项的意思，返回值为数组的各项的意思
  * 参数 $queryLimit
  * |-start  起始条数
  * |-num   每页条数
  * |-sort   排序（根据自己的传入的字段，依照此字段排序）
  * |-desc   是否逆转排序即倒序(默认正序)
  * |-paginate  分页（使用laravel自动分页，这里指定数值）
  * |-id      按照字段id值，筛选某条记录
  * |*/
    /*
    * $return_data
    * |-status 是否成功
    * |-message 消息
    * |-data  查询结果值
    * |-total 符合查询条件的总条数，不是数据库总条数
     */

    /**
     * 筛选图片
     * @param $query_limit
     * @return mixed
     */
    public static function select($query_limit)
    {
        $query = DB::table("image");



        //按id查找某条记录
        if(isset($query_limit["id"]))
        {
            $query = $query->where("image_id","=",$query_limit["id"]);
        }

        //符合查询条件的总条数，不是数据库总条数
        $num_query  = clone $query;//克隆出来不适用原来的对象
        $return_data["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num  ;


        //排序
        if(  isset($query_limit["sort"])  )  //自定义字段排序
        {
            if(isset($query_limit["desc"]) && true == $query_limit["desc"])
            {
                $query = $query->orderBy($query_limit["sort"],"desc");
            }
            else
            {
                $query = $query->orderBy($query_limit["sort"]);
            }

        }
        else    //按id排序
        {
            if(isset($query_limit["desc"])  && true==$query_limit["desc"])
            {
                $query = $query->orderBy("image_id","desc");
            }
            else
            {
                $query = $query->orderBy("image_id");
            }
        }

        //起始条数
        if ( isset($query_limit["start"])  )
        {
            $query = $query->skip($query_limit["start"]);
        }

        //每页条数
        if(isset($query_limit["num"]))
        {
            if($query_limit["num"]==0)
            {
                $return_data["status"] = true;
                $return_data["message"] = "查询到数据,但num设为了0";
                $return_data["data"] =  null;
                return $return_data;
            }
            $query = $query->take($query_limit["num"]);
        }
        else
        {
            $query = $query->take(7);     //自己增加，默认5条
            $query_limit["num"] = 7;     //
        }

        //是否分页
        if(isset($query_limit["paginate"]))
        {

            $data = $query->simplePaginate($query_limit["paginate"]);
        }
        else
        {
            $data = $query->get();
        }

        $return_data["status"] = true;
        $return_data["message"] = "成功获取到数据";
        $return_data["data"] = $data;
        return $return_data;

    }

    /*
     * 上传图片(把图片移到本地文件夹,并且添到数据库)
     * @param $inputData
     * @param $file
     * @return image_id/false
     */
    public static function putImage($inputData,$file)
    {
        //验证字段
        $errorInfo["required"] = ":attribute必填";
        $errorInfo["max"] = ":attribute不应大于20字节";

        $validator = Validator::make($inputData,[
            'image_name' => 'required|max:20',
        ],$errorInfo);
        if($validator->fails()){
            $messages = $validator->errors();
            $errorStr = "";
            foreach ($messages->all() as $message) {
                $errorStr.=$message." | ";
            }
            throw new SysValidatorException("字段格式有错误！".$errorStr,"/admin_api_sImage");
        }

        //1.文件移动
        $storage_path = config("my_config.image_upload_dir"). session("user.user_id")."/";  //存贮文件的相对路径
        $path = $_SERVER['DOCUMENT_ROOT'].$storage_path;  //存贮文件的绝对路径
        $name = date('YmdHis') . session("user.user_id") . rand(1000, 9999) . "." . $file->getClientOriginalExtension();  //自动生成路径

        //2.数据库添加
        //获取与前端file相关的数据库量
        $inputData["image_format"] = $file->getClientOriginalExtension();   //文件格式
        $inputData["image_path"] = $storage_path.$name;  //相对路径/自动生成名
        $inputData["image_create_time"] = date('Y-m-d H:i:s');
        $inputData["image_update_time"] = date('Y-m-d H:i:s');

        DB::beginTransaction();
        $add = DB::table('image')
            ->insertGetId($inputData);
        if ($add)
        {
            //上传图片，成功时添加日志
            $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员上传图片成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $logData = "上传图片";
            DBLog::adminLog($message,$admin,$level,$logData);

            $moveReturn = $file->move($path, $name);  //移动文件到指定目录
            if($moveReturn)
            {
                DB::commit();  //若移动文件或添加进数据库失败，则事务回滚
                return $add;
            }
            return false;
        }
        else
        {
            //上传图片，失败时添加日志
            $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员上传图片失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $logData = "上传图片";
            DBLog::adminLog($message,$admin,$level,$logData);
            return false;
        }
    }


    /**
     * 访问图片
     * @param $image_id
     */
    public static function getImage($image_id)
    {
        if($image_id == 0)
        {
            header("Content-type:image/jpeg");
            readfile($_SERVER["DOCUMENT_ROOT"]."/images/default.jpg");
        }
        $imageData = DB::table("image")
            ->where("image_id","=",$image_id)
            ->first();
        if($imageData!=NULL)
        {
            $path =  $imageData->image_path;
            $format = $imageData->image_format;
            switch( $format ) {
                case "gif": $ctype="image/gif"; break;
                case "png": $ctype="image/png"; break;
                case "jpeg":
                case "jpg": $ctype="image/jpeg"; break;
                default: $ctype="image/jpeg";
            }
            header('Content-type: ' . $ctype);
            readfile($_SERVER['DOCUMENT_ROOT'].$path);  //读文件并返回
        }
        else //如果没有图片的，换上一张默认图片
        {
            header("Content-type:image/jpeg");
            readfile($_SERVER["DOCUMENT_ROOT"]."/images/default.jpg");
        }
    }


    public function delete()
    {
        $imageId = $this->image_id;
        $userId = session("user.user_id");
        $imageInfo = $this->info;
        //判断数据库是否存在此图片
        if ($imageInfo == false) {
            return false;
        }
        //判断此图片是否属于该用户
        if ($imageInfo->image_user != $userId) {
            return false;
        }
        //1.先删除数据库的
        DB::beginTransaction();    //开始事务
        $count = DB::table('image')->where('image_user', '=', $userId)->where('image_id', '=', $imageId)->delete();  //先删数据库的
        if ($count == 0) {
            return false;
        }

        //2.删文件里的
        $getPath = $_SERVER['DOCUMENT_ROOT'] . $imageInfo->image_path;  //提取路径
        if (unlink($getPath)) { //unlink是删除里面的路径
            DB::commit(); //提交事务
            return true;
        }
        return false;

    }



}