<?php
/*
 * This file is part of the wanglelecc/redis.
 *
 * (c) wanglele <wanglelecc@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Wanglelecc\Redis\Concerns;

use Illuminate\Support\Str;

trait SetAttributes
{
    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * 填充值
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if(property_exists($this, $key)){
                $this->$key = $value;
            }else{
                $this->setAttribute($value);
            }
        }

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributesToArray();
    }

    public function setAttribute(string $value)
    {
        $this->attributes[$value] = $value;

        return $this;
    }
    
    
    protected function hasJson($value){
        
        if( !is_string($value) ){ return false; }
        
        $json = json_encode($value);
        
        return JSON_ERROR_NONE === json_last_error();
    }
    
    public function attributesToArray()
    {
        return $this->attributes;
    }
    
    /**
     * Encode the given value as JSON.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function asJson($value)
    {
        return json_encode($value);
    }
    
    public function toArray()
    {
        return $this->attributesToArray();
    }
    
    public function __toString()
    {
        return $this->toJson();
    }
    
    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);
        
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw JsonEncodingException::forModel($this, json_last_error_msg());
        }
        
        return $json;
    }
    
    /**
     * Decode the given JSON back into an array or object.
     *
     * @param  string  $value
     * @param  bool  $asObject
     * @return mixed
     */
    public function fromJson($value, $asObject = false)
    {
        return json_decode($value, ! $asObject);
    }
    
    
    
    public function jsonSerialize()
    {
        return $this->toArray();
    }

}
