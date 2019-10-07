<?php
namespace Metronome\Tests\Form;

use Metronome\Auth\MetronomeUser;
use Metronome\Form\MetronomeEntityFormDataBuilder;
use Metronome\Form\MetronomeFormData;
use Metronome\Tests\Util\TestEntity;
use PHPUnit\Framework\TestCase;

class MetronomeEntityFormDataBuilderTest extends TestCase
{
    /** @var MetronomeEntityFormDataBuilder */
    private $dataBuilder;

    public function setUp()
    {
        parent::setUp();
        $this->dataBuilder = new MetronomeEntityFormDataBuilder();
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnNonNull() {
        $this->dataBuilder
            ->isValid(true)
            ->formData(new TestEntity())
        ;

        $result = $this->dataBuilder->build();

        $this->assertNotNull($result);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnMetronomeFormData() {
        $this->dataBuilder
            ->isValid(true)
            ->formData(new TestEntity())
        ;

        $result = $this->dataBuilder->build();

        $this->assertTrue($result instanceof MetronomeFormData);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedData() {
        $this->dataBuilder
            ->isValid(true)
            ->formData(new TestEntity())
        ;

        $result = $this->dataBuilder->build();

        $this->assertNotNull($result->getSubmittedData());
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnCorrectObject() {
        $this->dataBuilder
            ->isValid(true)
            ->formData(new TestEntity())
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData();
        $this->assertTrue($data instanceof TestEntity);
    }

    public function test_givenBuilder_andIsNotValid_andFormErrorsSet_thenShouldReturnNonNull() {
        $this->dataBuilder
            ->error("not_blank", "The value was blank")
        ;

        $result = $this->dataBuilder->build();

        $this->assertNotNull($result);
    }

    public function test_givenBuilder_andIsNotValid_andFormErrorsSet_thenShouldReturnMetronomeFormData() {
        $this->dataBuilder
            ->error("not_blank", "The value was blank")
        ;

        $result = $this->dataBuilder->build();

        $this->assertTrue($result instanceof MetronomeFormData);
    }

    public function test_givenBuilder_andIsNotValid_andFormErrorsSet_thenShouldReturnErrors() {
        $this->dataBuilder
            ->error("not_blank", "The value was blank")
        ;

        $result = $this->dataBuilder->build();

        $errors = $result->getErrors();
        $this->assertNotNull($errors);
    }

    public function test_givenBuilder_andIsNotValid_andFormErrorsSet_thenShouldReturnError() {
        $this->dataBuilder
            ->error("not_blank", "The value was blank")
        ;

        $result = $this->dataBuilder->build();

        $errors = $result->getErrors();
        $this->assertArrayHasKey("not_blank", $errors);
    }

    public function test_givenBuilder_andIsNotValid_andFormErrorsSet_thenShouldReturnErrorObject() {
        $this->dataBuilder
            ->error("not_blank", "The value was blank")
        ;

        $result = $this->dataBuilder->build();

        $errors = $result->getErrors();
        $error = $errors["not_blank"];
        $this->assertNotNull($error);
    }

    public function test_givenBuilder_andIsNotValid_andFormErrorsSet_thenShouldReturnErrorClass() {
        $this->dataBuilder
            ->error("not_blank", "The value was blank")
        ;

        $result = $this->dataBuilder->build();

        $errors = $result->getErrors();
        $error = $errors["not_blank"];
        $this->assertEquals("The value was blank", $error);
    }

    public function test_givenBuilder_whenIsValid_thenShouldReturnThis() {
        $result = $this->dataBuilder->isValid(true);
        $this->assertTrue($result instanceof MetronomeEntityFormDataBuilder);
    }

    public function test_givenBuilder_whenFormData_thenShouldReturnThis() {
        $result = $this->dataBuilder->formData(new MetronomeUser());
        $this->assertTrue($result instanceof MetronomeEntityFormDataBuilder);
    }

    public function test_givenBuilder_whenError_thenShouldReturnThis() {
        $result = $this->dataBuilder->error("someError", "error");
        $this->assertTrue($result instanceof MetronomeEntityFormDataBuilder);
    }
}
