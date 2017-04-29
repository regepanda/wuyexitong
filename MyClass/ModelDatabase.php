<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/11
 * Time: 10:38
 */

namespace MyClass;
use Illuminate\Support\Facades\DB;

class ModelDatabase
{
    public $info; //一条记录信息
    public $id; //一条记录的id号
    public $isNull; //控制器实例化对象的时候看看传过来的id是否有效
    public $prefix;//表的主键
    public $table = "liangshuaibiao";

    /*
     * 查询
     * @param $queryLimit
     * @return 标准结构
     */
    public final function select($queryLimit)
    {
        /*
        * $queryLimit
        * |-start  起始
        * |-num   每页条数
        * |-class  类别（如果有）
        * |-sort   排序
        * |-desc   是否逆转排序即倒序(默认正序)
        * |-id       限制id（制定一个固定id)
        * |*/

        /*
         * $returnData
         * |-status 是否成功
         * |-message 消息
         * |-total    数据总条数(不是当前数据的条数，是可按照此限制查出的所有数据)
         * |-data   数据 DB返回的二维结构,第二位是jSON
         *
         */
        $query = DB::table($this->table);
        $this->selectExtra($queryLimit);
        //按id查找某条记录
        if (isset($queryLimit["id"])) {
            $query = $query->where($this->table."_id", "=", $queryLimit["id"]);
        }

        //排序
        if (isset($queryLimit["sort"]))  //自定义字段排序
        {
            if (isset($queryLimit["desc"]) && true == $queryLimit["desc"]) {
                $query = $query->orderBy($queryLimit["sort"], "desc");
            } else {
                $query = $query->orderBy($queryLimit["sort"]);
            }

        } else    //按id排序
        {
            if (isset($queryLimit["desc"]) && true == $queryLimit["desc"]) {
                $query = $query->orderBy($this->table."_id", "desc");
            } else {
                $query = $query->orderBy($this->table."_id");
            }
        }

        //计算出符合查询条件的总条数
        $num_query = clone $query;//克隆出来不适用原来的对象
        $return_data["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num;

        //起始条数
        if (isset($queryLimit["start"])) {
            $query = $query->skip($queryLimit["start"]);
        }

        //每页条数
        if (isset($queryLimit["num"])) {
            if ($queryLimit["num"] == 0) {
                $return_data["status"] = true;
                $return_data["message"] = "查询到数据,但num设为了0";
                $return_data["data"] = null;
                return $return_data;
            }

            $query = $query->take($queryLimit["num"]);
        } else {
            $query = $query->take(10);
            $queryLimit["num"] = 10;
        }

        //是否分页
        if (isset($queryLimit["paginate"])) {

            $data = $query->simplePaginate($queryLimit["paginate"]);
        } else {
            $data = $query->get();
        }

        $return_data["status"] = true;
        $return_data["message"] = "成功获取到数据";
        $return_data["data"] = $data;
        return $return_data;
    }

    /*
     * 在select查询之前会调用这个函数，在这里添加一些每个类自定义的操作
     * 不要返回值，这里传的引用
     * @param $queryLimit
     * @param $cursor
     */
    public function selectExtra(&$queryLimit)
    {

    }

    /*
     * 添加
     * @param $dataArray
     * @return $id
     */
    public final function add($dataArray)
    {
        $dataArray[$this->table . "_create_time"] = date("Y-m-d H-i-s");
        $dataArray[$this->table . "_update_time"] = date("Y-m-d H-i-s");
        self::selectExtra($dataArray);
        $result = DB($this->table)->insertGetId($dataArray);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /*
     * 在add查询之前会调用这个函数，在这里添加一些每个类自定义的操作
     * 不要返回值，这里传的引用
     * @param $dataArray
     */
    public function addExtra(&$dataArray)
    {

    }

    /*
     * 构造函数，会自动刷新数据
     * @param $id string
     */
    public function __construct($id = null)
    {
        if ($id == null) {
            $this->isNull = true;
            return;
        } else {
            $this->isNull = false;
        }

        $this->id = $id;
        $this->getInfo();
        $this->isNull = false;
    }

    /*
     * 这个对象是否为空
     * @return bool
     */
    public function isNull()
    {
        if($this->isNull)
        {
            return true;
        }

        $result = $this->select([$this->table."_id"=>$this->id]);
        if(empty($result['data']))
        {
            return true;
        }
        return false;
    }

    /*
     *刷新信息
     */
    public function getInfo()
    {
        if($this->isNull()){return false;}
        $this->info = DB::table($this->table)->where($this->table."_id","=",$this->id)->first();
        return $this->info;
    }

    /*
     * 删除
     * @return true|false
     */
    public final function delete()
    {
        if($this->isNull()){return false;}
        $this->deleteExtra();
        $result = DB::table($this->table)->where($this->table."_id","=",$this->id)->delete();
        if($result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     **在delete之前会调用这个函数，在这里添加一些每个类自定义的操作
     * 一般不需要返回值
     */
    public function deleteExtra()
    {

    }

    /*
     * 更新，传入你要更新的字段即可
     * @param $dataArray
     * @return BSONDocument | false 成功返回最新的数据集，失败或者无改动false
     */
    public function update($dataArray)
    {
        if($this->isNull()){return false;}
        $dataArray[$this->table."update_time"] = date("Y-m-d H:i:s");
        $this->updateExtra($dataArray);
        $result = DB::table($this->table)->where($this->table."_id","=",$this->id)->update();
        if($result)
        {
            return $this->getInfo();
        }
        else
        {
            return false;
        }
    }

    /*
     * 在update之前会调用这个函数，在这里添加一些每个类自定义的操作
     * 一般不需要返回值,注意传入的是引用
     * @param $dataArray 传入修改数据
     */
    public function updateExtra(&$dataArray)
    {

    }
}