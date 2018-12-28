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

use Wanglelecc\Redis\Concerns\ZsetAttributes;

abstract class ZsetModel extends Model
{
    use ZsetAttributes;
    
    // 存储类型
    protected $type = 'zset';
    
    // 过期时间
    protected $expired = 180;
    
    // 键名
    protected $table = "";


    public $id;
    
    /**
     * 保存
     *
     * @return $this|bool
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function save()
    {
        $this->validate();

        $key = $this->getTable();

        if( $this->add($this->attributes) ){
            $this->setExpired($key);
            return $this;
        }

        return false;
    }

    /**
     * 添加元素
     *
     * @param $attributes
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function add(array $attributes)
    {
        $this->validate();
        
        return $this->getConnection()->zadd($this->getTable(), $attributes);
    }
    
    /**
     * 获取集合所有成员
     *
     * @return SetModel
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function all()
    {
        $this->validate();
        
        return $this->fill($this->range());
    }

    /**
     * 获取集合成员数
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function card()
    {
        $this->validate();

        return $this->getConnection()->zcard($this->getTable());
    }
    
    /**
     *  计算在有序集合中指定区间分数的成员数
     *
     * @param int $min
     * @param int $max
     *
     * @return string
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function count(int $min, int $max)
    {
        $this->validate();
        
        return $this->getConnection()->zcount($this->getTable(), $min, $max);
    }
    
    /**
     * 在有序集合中计算指定字典区间内成员数量
     *
     * @param $min
     * @param $max
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function lexcount($min, $max){
        $this->validate();
    
        return $this->getConnection()->zlexcount($this->getTable(), $min, $max);
    }
    
    /**
     * 自增
     *
     * @param     $member
     * @param int $increment
     *
     * @return string
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function increment($member, int $increment = 1)
    {
        $key = $this->validate();
        
        return $this->getConnection()->zincrby( $this->getTable(), $increment, $member);
    }
    
    /**
     * 自减
     *
     * @param     $member
     * @param int $increment
     *
     * @return string
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function decrement($member, int $increment = 1)
    {
        $this->validate();
        
        if($amount > 0){ $amount = 0 - $amount; }
        
        return $this->getConnection()->zincrby( $this->getTable(), $increment, $member);
    }
    
    /**
     * 通过索引区间返回有序集合成指定区间内的成员
     *
     * @param int    $start
     * @param int    $stop
     * @param string $option
     *
     * @return array
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function range(int $start = 0, int $stop = -1)
    {
        $this->validate();
    
        $option = ['WITHSCORES'];
    
        $value = $this->getConnection()->zrange($this->getTable(), $start, $stop, (array)$option);
        
        $attributes = $this->valueToArray($value);
        
        return $attributes;
    }
    
    /**
     * 通过字典区间返回有序集合的成员
     *
     * @param $min
     * @param $max
     *
     * @return array
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function rangebylex($min, $max)
    {
        $this->validate();
    
        $option = ['WITHSCORES'];
        
        $value = $this->getConnection()->zrangebylex($this->getTable(), $min, $max, (array)$option);
    
        $attributes = $this->valueToArray($value);
        
        return $attributes;
    }
    
    /**
     * 通过分数返回有序集合指定区间内的成员
     *
     * @param $min
     * @param $max
     *
     * @return array
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function rangebyscore($min, $max){
        
        $this->validate();
    
        $option = ['WITHSCORES'];
    
        $value = $this->getConnection()->zrangebyscore($this->getTable(), $min, $max, (array)$option);
    
        $attributes = $this->valueToArray($value);
        
        return $attributes;
    }
    
    /**
     * 返回有序集合中指定成员的索引
     *
     * @param $member
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function rank($member)
    {
        $this->validate();
        
        return $this->getConnection()->zrank($this->getTable(), $member);
    }
    
    /**
     * 移除成员
     *
     * @param $member
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function del($member)
    {
        $this->validate();
        
        return $this->getConnection()->zrank( $this->getTable(), $member);
    }
    
    /**
     * 移除成员别名
     *
     * @param $member
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function rem($member)
    {
        return $this->del($member);
    }
    
    /**
     * 除有序集合中给定的字典区间的所有成员
     *
     * @param $min
     * @param $max
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function remrangebylex($min, $max)
    {
        $this->validate();
        
        return $this->getConnection()->zremrangebylex($this->getTable(),$min, $max);
    }
    
    /**
     * 移除有序集合中给定的排名区间的所有成员
     *
     * @param $start
     * @param $stop
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function remrangebyrank($start, $stop)
    {
        $this->validate();
        
        return $this->getConnection()->zremrangebyrank($this->getTable(), $start, $stop);
    }
    
    /**
     * 移除有序集合中给定的分数区间的所有成员
     *
     * @param $start
     * @param $stop
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function remrangebyscore($start, $stop)
    {
        $this->validate();
    
        return $this->getConnection()->zremrangebyscore($this->getTable(), $start, $stop);
    }
    
    /**
     * 回有序集中指定分数区间内的成员，分数从高到低排序
     *
     * @param $start
     * @param $stop
     *
     * @return array
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function revrangebyscore($start, $stop){
        
        $this->validate();
        
        $option = ['WITHSCORES'];
        
        $value = $this->getConnection()->zrevrangebyscore($this->getTable(), $start, $stop, (array)$option);
    
        $attributes = $this->valueToArray($value);
        
        return $attributes;
    }
    
    /**
     * 返回有序集中指定区间内的成员，通过索引，分数从高到底
     *
     * @param $start
     * @param $stop
     *
     * @return array
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function revrange($start, $stop){
        
        $this->validate();
        
        $option = ['WITHSCORES'];
        
        $value = $this->getConnection()->zrevrange($this->getTable(), $start, $stop, (array)$option);
    
        $attributes = $this->valueToArray($value);
        
        return $attributes;
    }
    
    /**
     * 返回有序集合中指定成员的排名，有序集成员按分数值递减(从大到小)排序
     *
     * @param $member
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function revrank($member){
        
        $this->validate();
    
        return $this->getConnection()->zrevrank($this->getTable(), $member);
    }
    
    /**
     * 获取分值
     *
     * @param $member
     *
     * @return string
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function score($member){
        $this->validate();
        
        return $this->getConnection()->zscore($this->getTable(), $member);
    }
    
    /**
     * 整理数组结构
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