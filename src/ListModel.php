<?php

/*
 * This file is part of the wanglelecc/redis.
 *
 * (c) wanglele <wanglelecc@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Wanglelecc\Redis;

abstract class ListModel extends Model
{
    
    // 存储类型
    protected $type = 'list';
    
    // 过期时间
    protected $expired = 180;
    
    // 键名
    protected $table = "";
    
    public $id;
    
    /**
     * 移出并获取列表的第一个元素， 如果列表没有元素会阻塞列表直到等待超时或发现可弹出元素为止
     *
     * @param int $timeout
     *
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function bloop($timeout = 2)
    {
        $this->validate();
        
        $value = $this->getConnection()->blpop([$this->getTable()], $timeout);
        
        $value = $this->valueToArray($value);
        
        return array_keys($value);
    }
    
    /**
     * 移出并获取列表的最后一个元素， 如果列表没有元素会阻塞列表直到等待超时或发现可弹出元素为止
     *
     * @param int $timeout
     *
     * @return array
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function brpop($timeout = 2)
    {
        $this->validate();
        
        $value = $this->getConnection()->brpop([$this->getTable()], $timeout);
        
        $value = $this->valueToArray($value);
        
        return array_keys($value);
    }
    
    /**
     * 从列表中弹出一个值，将弹出的元素插入到另外一个列表中并返回它； 如果列表没有元素会阻塞列表直到等待超时或发现可弹出元素为止
     *
     * @param     $destination
     * @param int $timeout
     *
     * @return array
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function brpoplpush($destination, $timeout = 2)
    {
        $this->validate();
        
        $value = $this->getConnection()->brpoplpush($this->getTable(), $destination, $timeout);
        
        $value = $this->valueToArray($value);
        
        return array_keys($value);
    }
    
    /**
     * 通过索引获取列表中的元素
     *
     * @param $index
     *
     * @return string
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function index($index){
        $this->validate();
        
        return $this->getConnection()->lindex($this->getTable(), $index);
    }
    
    /**
     * 在列表的元素前或者后插入元素
     *
     * @param $whence
     * @param $pivot
     * @param $value
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function insert($whence, $pivot, $value){
        $this->validate();
    
        return $this->getConnection()->linsert($this->getTable(), $whence, $pivot, $value);
    }
    
    /**
     * 获取列表长度
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function len(){
        $this->validate();
        
        return $this->getConnection()->llen($this->getTable());
    }
    
    /**
     * 移出并获取列表的第一个元素
     *
     * @return string
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function lpop()
    {
        $this->validate();
        
        return $this->getConnection()->lpop($this->getTable());
    }
    
    /**
     * 将一个或多个值插入到列表头部
     *
     * @param $values
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function lpush($values)
    {
        $this->validate();
        
        return $this->getConnection()->lpush($this->getTable(), (array)$values);
    }
    
    /**
     * 将一个或多个值插入到已存在的列表头部
     *
     * @param $values
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function lpushx($values)
    {
        $this->validate();
        
        return $this->getConnection()->lpushx($this->getTable(), $values);
    }
    
    /**
     * 获取列表指定范围内的元素
     *
     * @param $start
     * @param $stop
     *
     * @return array
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function lrange($start, $stop)
    {
        $this->validate();
        
        return $this->getConnection()->lrange($this->getTable(), $start, $stop);
    }
    
    /**
     * 移除列表元素
     *
     * COUNT 的值可以是以下几种：
     * count > 0 : 从表头开始向表尾搜索，移除与 VALUE 相等的元素，数量为 COUNT 。
     * count < 0 : 从表尾开始向表头搜索，移除与 VALUE 相等的元素，数量为 COUNT 的绝对值。
     * count = 0 : 移除表中所有与 VALUE 相等的值。
     *
     * @param $count
     * @param $value
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function lrem($count, $value)
    {
        $this->validate();
    
        return $this->getConnection()->lrem($this->getTable(), $count, $value);
    }
    
    /**
     * 通过索引设置列表元素的值
     *
     * @param $index
     * @param $value
     *
     * @return mixed
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function lset($index, $value){
        $this->validate();
    
        return $this->getConnection()->lset($this->getTable(), $index, $value);
    }
    
    /**
     * 对一个列表进行修剪,只保留指定区间内的元素
     *
     * @param $start
     * @param $stop
     *
     * @return mixed
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function ltrim($start, $stop)
    {
        $this->validate();
    
        return $this->getConnection()->ltrim($this->getTable(), $start, $stop);
    }
    
    /**
     * 移除并获取列表最后一个元素
     *
     * @return string
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function rpop()
    {
        $this->validate();
    
        return $this->getConnection()->rpop($this->getTable());
    }
    
    /**
     * 移除列表的最后一个元素，并将该元素添加到另一个列表并返回
     *
     * @param $destination
     *
     * @return string
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function rpoplpush($destination){
        $this->validate();
    
        return $this->getConnection()->rpoplpush($this->getTable(), $destination);
    }
    
    /**
     * 在列表中添加一个或多个值
     *
     * @param $values
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function rpush($values)
    {
        $this->validate();
    
        return $this->getConnection()->rpush($this->getTable(), (array)$values);
    }
    
    /**
     * 为已存在的列表添加值
     *
     * @param $values
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function rpushx($values)
    {
        $this->validate();
        
        return $this->getConnection()->rpushx($this->getTable(), (array)$values);
    }
    
    /**
     * 获取返回值
     *
     * @param array $values
     *
     * @return array
     */
    protected function valueToArray(array $values){
        $array = [];
        
        for( $i = 0, $len = count($values); $i < $len; $i + 2 ){
            $array[$values[$i]] = $values[$i+1];
        }
        
        return $array;
    }
    
    public function __call($method, $parameters)
    {
        return $this->getConnection()->{$method}(...$parameters);
    }
}