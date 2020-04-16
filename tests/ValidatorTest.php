<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Helper\Validator;
use App\Db\BaseMapObject;

final class ValidatorTest extends TestCase
{
    protected $class;
    protected $baseObj;
    protected $baseObj2;

    protected $toArray = [
        'a' => 'value'
    ];
    protected $wrongDataTypes = [
        'a' => 2
    ];
    protected $wrongValueTypes = [
        'a' => Validator::INT
    ];

    public function setUp(): void {
        $this->class = new Validator();

        // base obj
        $this->baseObj = $this->createStub(BaseMapObject::class);
        
        $this->baseObj->expects($this->any())
            ->method('toArray')->willReturn($this->toArray);
        
        $this->baseObj->expects($this->any())
            ->method('getDataTypes')->willReturn($this->wrongDataTypes);   
    
        // base obj2
        $this->baseObj2 = $this->createStub(BaseMapObject::class);

        $this->baseObj2->expects($this->any())
            ->method('toArray')->willReturn($this->toArray);
        
        $this->baseObj2->expects($this->any())
            ->method('getDataTypes')->willReturn($this->wrongValueTypes);   
    
    }


    public function testThrowsInvalidDataTypeError(): void
    {
        $name = array_keys($this->toArray)[0];
        $this->expectExceptionMessage("Invalid data type for $name");

        $this->class->validate($this->baseObj);
    }


    public function testThrowsInvalidValue(): void
    {
        $name = array_keys($this->toArray)[0];
        $this->expectExceptionMessage("Invalid value for $name");

        $this->class->validate($this->baseObj2);
    }
}
