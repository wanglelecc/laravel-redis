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

use Wanglelecc\Redis\Concerns\StringAttributes;

abstract class StringModel extends Model
{
    use StringAttributes;
    
    // 存储类型
    protected $type = 'string';
    
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

        if( $this->set($this->attributes) ){
            $this->setExpired($key);
            return $this;
        }

        return false;
    }
    
    /**
     * 保存
     *
     * @param $value
     *
     * @return mixed
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function set($value)
    {
        $this->validate();
        
        return $this->getConnection()->set($this->getTable(), $value);
    }
    
    /**
     * 获取值
     *
     * @return string
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function get()
    {
        $this->validate();
        
        $value = $this->getConnection()->get($this->getTable());
        
        $this->setAttribute($value);
        
        return $value;
    }
    
    /**
     * 返回 key 中字符串值的子字符
     *
     * @param $start
     * @param $end
     *
     * @return string
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function getrange($start, $end)
    {
        $this->validate();
        
        return $this->getConnection()->getrange($this->getTable(), $start, $end);
    }
    
    /**
     * 将给定 key 的值设为 value ，并返回 key 的旧值(old value)
     *
     * @param $value
     *
     * @return string
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function getset($value)
    {
        $this->validate();
    
        return $this->getConnection()->getset($this->getTable(), $value);
    }
    
    /**
     * 只有在 key 不存在时设置 key 的值
     *
     * @param $value
     *
     * @return mixed
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function setnx($value)
    {
        $this->validate();
    
        return $this->getConnection()->set($this->getTable(), $value);
    }
    
    /**
     * 自增
     *
     * @param int $amount
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function increment(int $amount = 1)
    {
        $key = $this->validate();
        
        return $this->getConnection()->incrby( $this->getTable(), $amount);
    }
    
    /**
     * 自减
     *
     * @param int $amount
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function decrement(int $amount = 1)
    {
        $this->validate();
        
        return $this->getConnection()->decrby( $this->getTable(), $amount);
    }
    
    /**
     * 追加内容
     *
     * @param $value
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function append($value)
    {
        $this->validate();
        
        return $this->getConnection()->append($this->getTable(), $value);
    }
    
    
    public function __call($method, $parameters)
    {
        return $this->getConnection()->{$method}(...$parameters);
    }
}