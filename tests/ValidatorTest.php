<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Helper\Validator;
use App\Db\BaseMapObject;

final class ValidatorTest extends TestCase
{
    protected $stringValue = ['a' => 'value'];
    protected $intValue  = ['a' => 2];
    protected $nullValue = ['a' => null];

    protected $dataType  = ['a' => Validator::INT];
    protected $invalidDataType = ['a' => 'b'];

    protected $required = ['a'];


    public function setUp(): void {
        // Correct values
        $this->objWithInt = $this->createStub(BaseMapObject::class);
        $this->objWithInt->expects($this->any())
            ->method('toArray')->willReturn($this->intValue);
        $this->objWithInt->expects($this->any())
            ->method('getRequired')->willReturn($this->required);
        $this->objWithInt->expects($this->any())
            ->method('getDataTypes')->willReturn($this->dataType);  
        
        
        // Null value but correct since it is not required
        $this->objWithValidNull = $this->createStub(BaseMapObject::class);
        $this->objWithValidNull->expects($this->any())
            ->method('toArray')->willReturn($this->nullValue);
        $this->objWithValidNull->expects($this->any())
            ->method('getRequired')->willReturn([]);
        $this->objWithValidNull->expects($this->any())
            ->method('getDataTypes')->willReturn($this->dataType);  
        

        // Invalid Data Type
        $this->objWithInvalidDataType = $this->createStub(BaseMapObject::class);
        $this->objWithInvalidDataType->expects($this->any())
            ->method('toArray')->willReturn($this->intValue);
        $this->objWithInvalidDataType->expects($this->any())
            ->method('getRequired')->willReturn($this->required);
        $this->objWithInvalidDataType->expects($this->any())
            ->method('getDataTypes')->willReturn($this->invalidDataType);   
    

        // Null value for required param
        $this->objWithNull = $this->createStub(BaseMapObject::class);
        $this->objWithNull->expects($this->any())
            ->method('toArray')->willReturn($this->nullValue);
        $this->objWithNull->expects($this->any())
            ->method('getRequired')->willReturn($this->required);
        $this->objWithNull->expects($this->any())
            ->method('getDataTypes')->willReturn($this->dataType);
            

        // Invalid values for declared data type
        $this->objWithInvalidValue = $this->createStub(BaseMapObject::class);
        $this->objWithInvalidValue->expects($this->any())
            ->method('toArray')->willReturn($this->stringValue);
        $this->objWithInvalidValue->expects($this->any())
            ->method('getRequired')->willReturn($this->required);
        $this->objWithInvalidValue->expects($this->any())
            ->method('getDataTypes')->willReturn($this->dataType);

    }

    /**
     * 
     */
    public function testThrowsInvalidDataTypeError(): void
    {
        $name = array_keys($this->dataType)[0];
        $this->expectExceptionMessage("Invalid data type declared for $name");

        Validator::validate($this->objWithInvalidDataType);
    }

    /**
     * 
     */
    public function testThrowsEmptyValue(): void
    {
        $name = array_keys($this->dataType)[0];
        $this->expectExceptionMessage("Parameter $name cannot be empty!");

        Validator::validate($this->objWithNull);
    }

    /**
     * 
     */
    public function testThrowsInvalidValue(): void
    {
        $name = array_keys($this->dataType)[0];
        $this->expectExceptionMessage("Invalid value for $name.");

        Validator::validate($this->objWithInvalidValue);
    }

    /**
     * 
     */
    public function testIsValidationOk(): void
    {
        $this->assertEquals(null, Validator::validate($this->objWithInt));
    }

    /**
     * 
     */
    public function testIsValidationOkEvenIfNull(): void
    {
        $this->assertEquals(null, Validator::validate($this->objWithValidNull));
    }
}
