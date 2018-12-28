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

use Wanglelecc\Redis\Concerns\SetAttributes;

abstract class SetModel extends Model
{
    use SetAttributes;
    
    // 存储类型
    protected $type = 'set';
    
    // 过期时间
    protected $expired = 180;
    
    // 键名
    protected $table = "";


    public $id;


    public function save()
    {
        $this->validate();

        $key = $this->getTable();

        if( $this->add(array_keys($this->attributes)) ){
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
    public function add($attributes)
    {
        $this->validate();

        $attributes = (array)$attributes;

        return $this->getConnection()->sadd($this->getTable(), array_values($attributes));
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
    
        $attributes = $this->getConnection()->smembers($this->getTable());
        
        return $this->fill($attributes);
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

        return $this->getConnection()->scard($this->getTable());
    }

    /**
     * 返回给定所有集合的差集
     *
     * @param $keys
     * @return array
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function diff($keys){

        $this->validate();

        $keys = (array)$keys;

        array_unshift($keys, $this->getTable());

        return $this->getConnection()->sdiff(array_unique($keys));
    }
    
    /**
     * 计算交集
     *
     * @param $keys
     *
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function inter($keys)
    {
        $this->validate();
    
        $keys = (array)$keys;
    
        array_unshift($keys, $this->getTable());
    
        return $this->getConnection()->sinter (array_unique($keys));
    }
    
    /**
     * 计算并集
     *
     * @param $keys
     *
     * @return array
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function union($keys)
    {
        $this->validate();
    
        $keys = (array)$keys;
    
        array_unshift($keys, $this->getTable());
    
        return $this->getConnection()->sunion(array_unique($keys));
    }
    
    /**
     * 移除成员
     *
     * @param $member
     *
     * @return bool
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function del($member)
    {
        $this->validate();
        
        return (bool)$this->getConnection()->srem( $this->getTable(), $member);
    }
    
    /**
     * 移除并返回集合中的元素
     *
     * @param null $count
     *
     * @return string
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function pop($count = null)
    {
        $this->validate();
        
        return $this->getConnection()->spop($this->getTable(), $count);
    }
    
    /**
     * 判断元素是否存在
     *
     * @param $value
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function has($value){
        $this->validate();
        
        return $this->getConnection()->sismember($this->getTable(), $value);
    }


    public function __call($method, $parameters)
    {
        return $this->getConnection()->{$method}(...$parameters);
    }
}