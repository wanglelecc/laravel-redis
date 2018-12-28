<?php
/**
 * Created by PhpStorm.
 * User: lele.wang
 * Date: 2018/10/29
 * Time: 8:38
 */

namespace Wanglelecc\Redis;

use Lock\Lock as BaseLock;
use Wanglelecc\Redis\Exceptions\LockTimeoutException;

class Lock extends StringModel
{
    // 锁的过期时间
    protected $expired = 3;
    
    // 默认超时时间，单位：毫秒
    protected $timeout = 30000;
    
    // 步长
    protected $sleep = 200;
    
    // 锁的类别
    protected $category = 'default';
    
    // 键名
    protected $table = "lock:%category";
    
    /**
     * 抢占锁
     *
     * 多进程并发时, 其中某一个进程得到锁后, 其他进程将被拒绝
     *
     * @param \Closure $callback
     * @param          $id
     * @param null     $category
     * @param null     $timeout
     *
     * @return mixed
     * @throws Exceptions\ModelLackArrtibutesException
     * @throws LockTimeoutException
     */
    public function query(\Closure $callback, $id, $category = null, $timeout = null){
    
        $this->id   = $id;
        $category   ?? $this->category  = $category;
    
        try{
            return BaseLock::lock($callback, $this->getTable());
        }catch (\Exception $exception){
            throw new LockTimeoutException('Get lock timeout by Lock:'.$this->getTable());
        }
    
    }
    
    
    /**
     * 队列锁
     *
     * 多进程并发时, 其中某一个进程得到锁后, 其他进程将等待解锁(配置最大等待进程后, 超过等待数量后进程将被拒绝)
     *
     * @param \Closure $callback
     * @param          $id
     * @param null     $category
     * @param int      $max_queue_process 队列最大等待进程
     *
     * @return BaseLock
     * @throws LockTimeoutException
     */
    public function queue(\Closure $callback, $id, $category = null, $max_queue_process = 100){
        $this->id   = $id;
        
        $category   ?? $this->category  = $category;
        
        try{
            return BaseLock::queueLock($callback, $this->getTable(), $max_queue_process);
        }catch (\Exception $exception){
            throw new LockTimeoutException('Get lock timeout by Lock:'.$this->getTable());
        }
        
    }
    
    /**
     * 限流
     *
     * @param      $id
     * @param null $category
     * @param int  $period      限制时间(秒)
     * @param int  $max_count   限制时间内最大数量
     *
     * @return BaseLock
     */
    public function allowed($id, $category = null, $period = 3, $max_count = 100){
        $this->id   = $id;
        $category   ?? $this->category  = $category;
        
        return BaseLock::isActionAllowed($this->getTable(), $period, $max_count);
    }
    
    
    public static function __callStatic($name, $arguments){
        return (new static)->$method(...$parameters);
    }
}