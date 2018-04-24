<?php
namespace Metronome\Tests\Form;

use Metronome\Form\MetronomeFormData;
use Metronome\Form\MetronomeFormDataBuilder;
use Metronome\Tests\Util\FirstForm;
use Metronome\Tests\Util\SecondForm;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Util\StringUtil;

class MetronomeFormDataBuilderTest extends \PHPUnit_Framework_TestCase
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
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $this->assertNotNull($result);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnMetronomeFormData() {
        $this->dataBuilder
            ->isValid(true)
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $this->assertTrue($result instanceof MetronomeFormData);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedData() {
        $this->dataBuilder
            ->isValid(true)
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $this->assertNotNull($result->getSubmittedData());
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm1() {
        $formName1 = StringUtil::fqcnToBlockPrefix(FirstForm::class);
        $this->dataBuilder
            ->isValid(true)
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData();
        $this->assertArrayHasKey($formName1, $data);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm1Field1() {
        $formName1 = StringUtil::fqcnToBlockPrefix(FirstForm::class);
        $this->dataBuilder
            ->isValid(true)
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData()[$formName1];
        $this->assertArrayHasKey("field_one", $data);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm1Field1Value() {
        $formName1 = StringUtil::fqcnToBlockPrefix(FirstForm::class);
        $this->dataBuilder
            ->isValid(true)
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData()[$formName1];
        $value = $data["field_one"];
        $this->assertEquals("123456", $value);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm1Field2() {
        $formName1 = StringUtil::fqcnToBlockPrefix(FirstForm::class);
        $this->dataBuilder
            ->isValid(true)
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData()[$formName1];
        $this->assertArrayHasKey("field_two", $data);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm1Field2Value() {
        $formName1 = StringUtil::fqcnToBlockPrefix(FirstForm::class);
        $this->dataBuilder
            ->isValid(true)
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData()[$formName1];
        $value = $data["field_two"];
        $this->assertEquals("abcdef", $value);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm2() {
        $formName1 = StringUtil::fqcnToBlockPrefix(SecondForm::class);
        $this->dataBuilder
            ->isValid(true)
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData();
        $this->assertArrayHasKey($formName1, $data);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm2Field1() {
        $formName1 = StringUtil::fqcnToBlockPrefix(SecondForm::class);
        $this->dataBuilder
            ->isValid(true)
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData()[$formName1];
        $this->assertArrayHasKey("field_a", $data);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm2Field1Value() {
        $formName1 = StringUtil::fqcnToBlockPrefix(SecondForm::class);
        $this->dataBuilder
            ->isValid(true)
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData()[$formName1];
        $value = $data["field_a"];
        $this->assertEquals("abcdef", $value);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm2Field2() {
        $formName1 = StringUtil::fqcnToBlockPrefix(SecondForm::class);
        $this->dataBuilder
            ->isValid(true)
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData()[$formName1];
        $this->assertArrayHasKey("field_b", $data);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm2Field2Value() {
        $formName1 = StringUtil::fqcnToBlockPrefix(SecondForm::class);
        $this->dataBuilder
            ->isValid(true)
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData()[$formName1];
        $value = $data["field_b"];
        $this->assertEquals("123456", $value);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm2Field3() {
        $formName1 = StringUtil::fqcnToBlockPrefix(SecondForm::class);
        $this->dataBuilder
            ->isValid(true)
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData()[$formName1];
        $this->assertArrayHasKey("field_c", $data);
    }

    public function test_givenBuilder_andIsValid_andValidValuesSetForMultipleForms_whenBuild_thenShouldReturnSubmittedDataForm2Field3Value() {
        $formName1 = StringUtil::fqcnToBlockPrefix(SecondForm::class);
        $this->dataBuilder
            ->isValid(true)
            ->formData(new FirstForm(), "field_one", "123456")
            ->formData(new FirstForm(), "field_two", "abcdef")
            ->formData(new SecondForm(), "field_a", "abcdef")
            ->formData(new SecondForm(), "field_b", "123456")
            ->formData(new SecondForm(), "field_c", "fedcba")
        ;

        $result = $this->dataBuilder->build();

        $data = $result->getSubmittedData()[$formName1];
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
