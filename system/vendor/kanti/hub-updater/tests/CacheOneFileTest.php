<?php
/**
 * Created by PhpStorm.
 * User: Matthias
 * Date: 27.01.2015
 * Time: 08:55
 */

namespace Kanti\Test;

use Kanti\CacheOneFile;

class CacheOneFileTest extends \PHPUnit_Framework_TestCase
{
    private $invalidContent = [
        true,
        false,
        array('content'),
    ];

    private $validContent = [
        '',
        'a',
        'ab',
        'abc',
        1,
        12,
        123,
    ];

    public function testIs()
    {
        $fileName = __DIR__ . "/asserts/testfile.txt";
        $time = 60*60;//1h
        if(file_exists($fileName)){
            unlink($fileName);
        }
        $cache = new CacheOneFile($fileName,$time);
        if($cache->is()){
            $this->fail("is not set");
        }

        touch($fileName);
        if(! $cache->is()){
            $this->fail("could not set");
        }
        touch($fileName,0);
        if($cache->is()){
            $this->fail("does not reset");
        }
    }

    public function testGet(){
        $fileName = __DIR__ . "/asserts/testfile.txt";
        $time = 60*60;//1h
        $cache = new CacheOneFile($fileName,$time);

        foreach ($this->validContent as $value) {
            $cache->set($value);
            if($cache->get() !== (string)$value){
                $this->fail("get set dosen't match for value{" . print_r($value,true) . "}");
            }
        }
        unlink($fileName);
        foreach ($this->invalidContent as $value) {
            $cache->set($value);
            if($cache->get() === $value){
                $this->fail("get set does match for value{" . print_r($value,true) . "}");
            }
        }
    }
}
 