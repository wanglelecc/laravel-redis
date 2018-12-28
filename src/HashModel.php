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

use Wanglelecc\Redis\Concerns\HashAttributes;
use Wanglelecc\Redis\Exceptions\HashModelNotFoundException;
use Wanglelecc\Redis\Exceptions\HashModelLackArrtibutesException;

abstract class HashModel extends Model
{

    use HashAttributes;

    // 存储类型
    protected $type = 'hash';
    
    // 过期时间
    protected $expired = 180;
    
    // 键名
    protected $table = "";
    
    protected $fillable = ['id'];

    /**
     * eGet 执行动作
     *
     * @param $key
     */
    protected function getAll($key) : array
    {
       return $this->getConnection()->hgetall($key);
    }

    /**
     * 保存
     *
     * @return $this|bool
     * @throws ModelLackArrtibutesException
     */
    public function save()
    {
        $this->validate();

        $key = $this->getTable();
        
        if( $this->getConnection()->hmset($key, $this->attributes) ){
            $this->setExpired($key);
            return $this;
        }

        return false;
    }

    /**
     * 获取对象
     *
     * @return HashModel
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function find()
    {
        $this->validate();
    
        $attr = $this->getAll($this->getTable());
        
        return $this->fill($attr);
    }
    
    /**
     * 自增
     *
     * @param     $column
     * @param int $amount
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function increment($column, int $amount = 1)
    {
        $key = $this->validate();
        
        return $this->getConnection()->hincrby( $this->getTable(), $column, $amount);
    }
    
    /**
     * 自减
     *
     * @param     $column
     * @param int $amount
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function decrement($column, int $amount = 1)
    {
        $this->validate();
        
        if($amount > 0){ $amount = 0 - $amount; }
    
        return $this->getConnection()->hincrby( $this->getTable(), $column, $amount);
    }
    
    /**
     * 获取 Keys
     *
     * @return array
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function keys()
    {
        $this->validate();
        
        return $this->getConnection()->hkeys( $this->getTable() );
    }
    
    /**
     * 获取长度
     *
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function len()
    {
        $this->validate();
    
        return $this->getConnection()->hlen( $this->getTable() );
    }
    
    /**
     * 判断否否存在某个值
     *
     * @param $column
     *
     * @return int
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function exists($field)
    {
        $this->validate();
    
        return (bool)$this->getConnection()->hexists( $this->getTable(), $field);
    }
    
    /**
     * 删除键
     *
     * @param $column
     *
     * @return bool
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function del($field)
    {
        $this->validate();
        
        if(!is_array($field)){
            $field = (array)$field;
        }
    
        return (bool)$this->getConnection()->hdel( $this->getTable(), $field);
    }
    
    /**
     * 设置哈希表字段的值
     *
     * @param $field
     * @param $value
     *
     * @return bool
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function set($field, $value)
    {
        $this->validate();
    
        return (bool)$this->getConnection()->hset( $this->getTable(), $field, $value);
    }
    
    /**
     * 只有在字段 field 不存在时,设置哈希表字段的值
     *
     * @param $field
     * @param $value
     *
     * @return bool
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function setNx($field, $value)
    {
        $this->validate();
    
        return (bool)$this->getConnection()->hsetnx( $this->getTable(), $field, $value);
    }
    
    /**
     * 同时保存多个值到哈希表中
     *
     * @param $dictionary
     *
     * @return bool|HashModel
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function mset($dictionary)
    {
        $this->validate();
    
        $dictionary = $this->fillableFromArray($dictionary);
        
        if( $this->getConnection()->hmset($this->getTable(), $dictionary) ){
            return $this->fill($dictionary);
        }
        
        return false;
    }
    
    /**
     * 获取 field 值
     *
     * @param $fields
     *
     * @return HashModel
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function get(string $field)
    {
        $this->validate();
        
        $value = $this->getConnection()->hget( $this->getTable(), $field);
        
        return $this->setAttribute($field, $value);
    }

    
    
    /**
     * 同时将多个 field 值
     *
     * @param $fields
     *
     * @return HashModel
     * @throws Exceptions\ModelLackArrtibutesException
     */
    public function mget($fields)
    {
        $this->validate();
        
        if(!is_array($fields)){
            $fields = func_get_args();
        }
    
        $values = $this->getConnection()->hmget( $this->getTable(), $fields);
        
        $attributes = [];
    
        foreach ($fields as $i => $field){
            $attributes[$field] = $values[$i];
        }
    
        return $this->fill($attributes);
    }


    public function __call($method, $parameters)
    {
        return $this->getConnection()->{$method}(...$parameters);
    }
}