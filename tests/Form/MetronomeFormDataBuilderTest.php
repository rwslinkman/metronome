<?php
namespace Metronome\Tests\Form;

use Metronome\Form\MetronomeFormData;
use Metronome\Form\MetronomeFormDataBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormError;

class MetronomeFormDataBuilderTest extends TestCase
{
    /** @var MetronomeFormDataBuilder */
    private $dataBuilder;

    public function setUp()
    {
        parent::setUp();
        $this->dataBuilder = new MetronomeFormDataBuilder();
        // TODO Write way more tests
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnNonNull() {
        $this->dataBuilder
            ->isValid(true)
            ->formData("field_two", "abcdef")
            ->formData("field_b", "123456")
            ->formData("field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $this->assertNotNull($result);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnMetronomeFormData() {
        $this->dataBuilder
            ->isValid(true)
            ->formData("field_two", "abcdef")
            ->formData("field_b", "123456")
            ->formData("field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $this->assertTrue($result instanceof MetronomeFormData);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedData() {
        $this->dataBuilder
            ->isValid(true)
            ->formData("field_two", "abcdef")
            ->formData("field_b", "123456")
            ->formData("field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $this->assertNotNull($result->getSubmittedData());
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm1Field2() {
        $this->dataBuilder
            ->isValid(true)
            ->formData("field_two", "abcdef")
            ->formData("field_b", "123456")
            ->formData("field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData();
        $this->assertArrayHasKey("field_two", $data);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm1Field2Value() {
        $this->dataBuilder
            ->isValid(true)
            ->formData("field_two", "abcdef")
            ->formData("field_b", "123456")
            ->formData("field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData();
        $value = $data["field_two"];
        $this->assertEquals("abcdef", $value);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm2Field2() {
        $this->dataBuilder
            ->isValid(true)
            ->formData("field_two", "abcdef")
            ->formData("field_b", "123456")
            ->formData("field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData();
        $this->assertArrayHasKey("field_b", $data);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm2Field2Value() {
        $this->dataBuilder
            ->isValid(true)
            ->formData("field_two", "abcdef")
            ->formData("field_b", "123456")
            ->formData("field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData();
        $value = $data["field_b"];
        $this->assertEquals("123456", $value);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm2Field3() {
        $this->dataBuilder
            ->isValid(true)
            ->formData("field_two", "abcdef")
            ->formData("field_b", "123456")
            ->formData("field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData();
        $this->assertArrayHasKey("field_c", $data);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm2Field3Value() {
        $this->dataBuilder
            ->isValid(true)
            ->formData("field_two", "abcdef")
            ->formData("field_b", "123456")
            ->formData("field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData();
        $value = $data["field_c"];
        $this->assertEquals("fedcba", $value);
    }

    public function test_givenBuilder_andIsNotValid_andFormErrorsSet_thenShouldReturnNonNull() {
        $this->dataBuilder
            ->error("not_blank", new FormError("The value was blank"))
        ;

        $result = $this->dataBuilder->build();

        $this->assertNotNull($result);
    }

    public function test_givenBuilder_andIsNotValid_andFormErrorsSet_thenShouldReturnMetronomeFormData() {
        $this->dataBuilder
            ->error("not_blank", new FormError("The value was blank"))
        ;

        $result = $this->dataBuilder->build();

        $this->assertTrue($result instanceof MetronomeFormData);
    }

    public function test_givenBuilder_andIsNotValid_andFormErrorsSet_thenShouldReturnErrors() {
        $this->dataBuilder
            ->error("not_blank", new FormError("The value was blank"))
        ;

        $result = $this->dataBuilder->build();

        $errors = $result->getErrors();
        $this->assertNotNull($errors);
    }

    public function test_givenBuilder_andIsNotValid_andFormErrorsSet_thenShouldReturnError() {
        $this->dataBuilder
            ->error("not_blank", new FormError("The value was blank"))
        ;

        $result = $this->dataBuilder->build();

        $errors = $result->getErrors();
        $this->assertArrayHasKey("not_blank", $errors);
    }

    public function test_givenBuilder_andIsNotValid_andFormErrorsSet_thenShouldReturnErrorObject() {
        $this->dataBuilder
            ->error("not_blank", new FormError("The value was blank"))
        ;

        $result = $this->dataBuilder->build();

        $errors = $result->getErrors();
        $error = $errors["not_blank"];
        $this->assertNotNull($error);
    }

    public function test_givenBuilder_andIsNotValid_andFormErrorsSet_thenShouldReturnErrorClass() {
        $this->dataBuilder
            ->error("not_blank", new FormError("The value was blank"))
        ;

        $result = $this->dataBuilder->build();

        $errors = $result->getErrors();
        $error = $errors["not_blank"];
        $this->assertTrue($error instanceof FormError);
    }
}
