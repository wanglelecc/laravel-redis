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

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use Wanglelecc\Redis\Exceptions\ModelLackArrtibutesException;

abstract class Model
{
    // string, list, set, hash, zset
    protected $type;

    protected $connection = null;

    protected $table;
    
    protected $expired = 0;
    
    protected $fillable = [];
    
    protected static $unguarded = false;
    
    protected static $resolver;
    
    protected static function boot()
    {
    }

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }
    
    public function getConnection()
    {
        return Redis::connection($this->connection);
    }
    
    public function getConnectionName()
    {
        return $this->connection;
    }
    
    public function setConnection($name)
    {
        $this->connection = $name;
        
        return $this;
    }
    
    /**
     * 参数验证
     *
     * @throws ModelLackArrtibutesException
     */
    protected function validate()
    {
        if (!isset($this->id)) {
            throw new ModelLackArrtibutesException('`id` cannot be null.');
        }
    }
    
    /**
     * 删除键
     *
     * @param $ids
     *
     * @return int
     */
    public static function destroy($ids)
    {
        $count = 0;
        
        if( !is_array($ids) ){
            $ids = func_get_args();
        }
        
        foreach($ids as $arg){
            if(!is_array($arg)){
                $arg = ['id' => $arg];
            }
    
            $instance = new static($arg);
            if( $instance->delete() ){
                ++$count;
            }
        }
        
        return $count;
    }
    
    /**
     * 删除
     *
     * @return int
     * @throws ModelLackArrtibutesException
     */
    public function delete()
    {
        $this->validate();
        return $this->getConnection()->del($this->getTable());
    }
    
    
    /**
     * 获取要保存的键名
     *
     * @return string
     */
    public function getTable()
    {
        $prefix = strtolower($this->type) . ':' . str_replace( '\\', '', Str::snake(Str::plural(class_basename($this))) ) . ':';
        
        $table = array_map(function($val){
            if( substr($val, 0, 1) === '%' && ( $key = str_replace('%', '', $val) ) ){
                
                if( !isset($this->$key) ){
                    throw new ModelLackArrtibutesException("Variabel: `{$key}` undefined");
                }
                
                return $this->$key;
            }
            return $val;
        }, array_filter(explode(':', strtolower($this->table))));
        
        return $table ? $prefix . implode(':', $table) . ':' . $this->id : $prefix . $this->id;
    }
    
    /**
     * 设置过期时间
     *
     * @param      $key
     * @param bool $pe
     */
    public function setExpired($key, $milli = false){
        if( $this->expired < 1){
            return;
        }
        
        return $milli ? $this->getConnection()->pexpire($key, $this->expired) : $this->getConnection()->expire($key, $this->expired);
    }
    
    /**
     * 设置键规则
     *
     * @param $table
     *
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;
        
        return $this;
    }
    


}
