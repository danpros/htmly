<?php
/**
 * Created by PhpStorm.
 * User: Matthias
 * Date: 27.01.2015
 * Time: 08:41
 */

namespace Kanti\Test;

use Kanti\HelperClass;

class HelperClassTest extends \PHPUnit_Framework_TestCase {

    private $invalidFileNames = [
        true,
        false,
        array('array'),
    ];

    private $validFileNames = [
        '',
        'a',
        'ab',
        'abc',
        1,
        12,
        123,
    ];

    public function testStaticFileExists()
    {
        foreach ($this->invalidFileNames as $value) {
            try {
                HelperClass::fileExists($value);
            } catch (\InvalidArgumentException $e) {
                // Good, we got an exception!
                continue;
            }
            $this->fail('Expected exception not raised on value: "' . $value . '".');
        }

        foreach ($this->validFileNames as $value) {
            HelperClass::fileExists($value);
        }
    }

    public function testIsInPhar(){
        if(HelperClass::isInPhar()){
            $this->fail("unit test is in phar?");
        }
    }
}
 