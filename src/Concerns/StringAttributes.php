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

trait StringAttributes
{
    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = null;
    
    
    public function hasAttributeExisted(){
        return !empty($this->attributes);
    }

    public function getAttributes()
    {
        if($this->hasJson($this->attributes)){
            return $this->fromJson($this->attributes);
        }
        
        return $this->attributes;
    }
    
    public function setAttribute($value)
    {
        
        if(is_array($value) || is_object($value)){
            $value = $this->asJson($value);
        }
        
        $this->attributes = $value;

        return $this;
    }
    
    protected function hasJson($value){
        
        if( !is_string($value) ){ return false; }
        
        $json = json_decode($value);
        
        return JSON_ERROR_NONE === json_last_error();
    }
    
    /**
     * Encode the given value as JSON.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function asJson($value)
    {
        return json_decode($value);
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
    
    
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if(property_exists($this, $key)){
                $this->$key = $value;
            }
        }
        
        return $this;
    }

}
