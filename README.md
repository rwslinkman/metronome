# Metronome
> Metronome is a lightweight test utility for Symfony 3 and Symfony 4 (PHP) applications.  
> It helps you orchestrate the tests for the Symfony in your hands, like a metronome should.

Using the `MetronomeBuilder` you can:
- Build a `MetronomeEnvironment` that sends GET and POST requests to test your `Controller` classes
- Build a mocked `EntityManager` to test classes that have database interactions
- Build a mocked `ReferenceRepository` to test your `Fixture` classes
- Build a mocked `ManagerRegistry` to test your `EntityRepository` classes  
- Inject `MetronomeLoginData` to bypass your `GuardAuthenticator` protection
- Mock `Symfony Forms`  using the `MetronomeFormDataBuilder` and `MetronomeEntityFormDataBuilder`
- Make use of Symfony's `Crawler` to see if your HTML is properly rendered
- Verify the contents of the Symfony `FlashBag`

## Installing Metronome
You can install Metronome using `composer` to get the package from Packagist.

```
composer require-dev rwslinkman/metronome
```

Metronome is not needed in the production environment, since it is a test utility.   
The latest version is `1.4.0`.      
For bleeding edge development, point to the `dev-develop` or `dev-master` version.

## Testing a Controller

Setup a `MetronomeEnvironment` using the `MetronomeBuilder`.   
The most basic test returns a `Response` from `Symfony\HttpFoundation`.   
Make sure your test extents `WebTestCase` to make use of Symfony's `Client` for HTTP requests.   


```php
class IndexControllerTest extends WebTestCase
{
    /** @var MetronomeBuilder */
    private $testEnvBuilder;
    
    public function setUp() {
        $this->testEnvBuilder = new MetronomeBuilder(static::createClient());
    }

    public function test_givenApp_whenGetIndex_thenShouldReturnOK() {
        /** @var MetronomeEnvironment */
        $testEnv = $this->testEnvBuilder->build();

        /** @var Response */
        $result = $testEnv->get("/");
        
        $this->assertEquals(200, $result->getStatusCode());
        // var_dump($result->getContent()); // Usefull for debugging!
    }
}
```

If you have custom services configured in Symfony's `services.yaml`, then you can mock those during tests.
This is convenient when testing `Controllers`.

```php
class IndexControllerTest extends WebTestCase
{
    /** @var MetronomeBuilder */
    private $testEnvBuilder;
    /** @var ProductServiceInjector */
    private $injector
    
    public function setUp() {
        $this->testEnvBuilder = new MetronomeBuilder(static::createClient());
        
        $this->injector = new ProductServiceInjector();
        $this->testEnvBuilder->injectService($this->injector);
    }
    
    public function test_givenApp_whenGetIndex_andEmptyProductList_thenShouldReturnOK() {
        // Inject the desired return value before building the environment.
        $this->injector->mockLoadAllProducts(array());
        /** @var MetronomeEnvironment */
        $testEnv = $this->testEnvBuilder->build();

        /** @var Response */
        $result = $testEnv->get("/");
        
        $this->assertEquals(200, $result->getStatusCode());
    }
}
```

You can use Metronome to mock your services by supplying a `ServiceInjector`.   
This overrides your service in the container.   
Add some setters to set your desired outcome for the mocked function.
```php
class ProductServiceInjector implements ServiceInjector { 
    private $loadAllProducts;

    /**
     * @return array Key => Value array of methods to mock and their respective results
     */
    public function inject()
    {
        return array(
            "loadAllProducts" => $this->loadAddProducts,
        );
    }

    /**
     * @return string The service name as defined in config.yml
     */
    public function serviceName()
    {
        return "rwslinkman.products";
    }

    /**
     * @return string Full namespace for the service to mock
     */
    public function serviceClass()
    {
        return '\rwslinkman\Service\ProductService';
    }
```

## Mocking the database and EntityManager
Classes that use the `Doctrine EntityManager` can be tested using Metronome.   
You can inject `RepoInjector` classes to mock your `EntityRepository`. 
```php
$metronomeBuilder = new MetronomeBuilder();
$metronomeBuilder->injectRepo(new ProductRepoInjector());
$entityManagerMock = $metronomeBuilder->buildEntityManager(ProductRepository::class);

$service = new ProductService($this->entityManager);
```

The `RepoInjector` is very similar to the `ServiceInjector`.   
It wraps around a single Doctrine `Entity`.   

```php
class ProductRepoInjector implements RepoInjector {
    private $findAll;
    
    /**
     * @return array Key => Value array of methods to mock and their respective results
     */
    public function inject()
    {
        return array(
            "findAll" => $this->findAll,
        );
    }

    /**
     * @return mixed Acts as an identifier for the repository
     */
    public function repositoryName()
    {
        return ProductRepository::class;
    }

    /**
     * @return string Full namespace for the repository to mock
     */
    public function repositoryClass()
    {
        return '\rwslinkman\Repository\ProductRepository';
    }
}
```

## Testing Fixtures
Fixtures are part of the `Doctrine FixtureBundle`.   
Metronome can be used to verify some of the `Fixture` behaviour.

```php
use PHPUnit\Framework\TestCase;

class MyFixtureTest extends TestCase
{
    public function test_givenFixture_whenLoad_shouldAlwaysPersist() {
        $envBuilder = new MetronomeBuilder();
        $mockEm = $envBuilder->buildEntityManager();

        $fixture = new MyFixture();
        $fixture->load($mockEm);

        $mockEm->shouldHaveReceived("flush");
    }
}
```

## Bypassing the Guard authentication system
When testing Controllers, you can bypass your `GuardAuthenticator` by injecting `MetronomeLoginData` into the environment builder.   
According to the Symfony documentation, the authenticator is defined in your `services.yaml`.   
This service identifier is used when creating `MetronomeLoginData`.
Use this to bypass the firewall configured in `security.yaml` during tests.

Using the `requiresLogin` function is not mandatory. If you want to test your firewall, omit the call.

```php
class AdminControllerTest extends WebTestCase
{
    /** @var MetronomeBuilder */
    private $testEnvBuilder;
    /** @var */ MetronomeLoginData */
    private $loginData;
    
    public function setUp() {
        $this->testEnvBuilder = new MetronomeBuilder(static::createClient());
        
        $myUser = new MyUser(); // implements UserInterface
        $this->loginData = new MetronomeLoginData($myUser, "rwslinkman.my_authenticator");
    }
    
    public function test_givenApp_whenGetIndex_andEmptyProductList_thenShouldReturnOK() {
        // Add this line to actually use the login data. 
        $this->testEnvBulder->requiresLogin($loginData);
        
        /** @var MetronomeEnvironment */
        $testEnv = $this->testEnvBuilder->build();

        /** @var Response */
        $result = $testEnv->get("/admin"); // A route that is protected by the firewall in security.yaml
        
        $this->assertEquals(200, $result->getStatusCode());
    }
}
```


## Testing forms in your Controllers
Most Symfony websites have forms in their Controllers, which can easily be tested with Metronome.   
Metronome provides 2 builders, that build a `MetronomeFormData` object.   
This data can be injected into the `MetronomeBuilder` to mock a form.

If you provide an instance of the object being modified to the Symfony FormBuilder, it is directly updated when submitting a valid form.   
You can mock this updated object by using the `MetronomeEntityFormDataBuilder`.
```php
$entity = new MyEntity();
$entityFormBuilder = new MetronomeEntityFormDataBuilder();
$entityFormBuilder
    ->formData($doctrineEntity)
    ->isValid(true);
$formData = $$entityFormBuilder->build();
```

If you have simpler forms, where you directly use the input data, you can make use of the `MetronomeFormDataBuilder`.   
It allows to directly inject values into the form fields.   
```php
// Simple forms
$formBuilder = new MetronomeFormDataBuilder();
$formBuilder
    ->isValid(true)
    ->formData("form_field_address", "some address")
    ->formData("form_field_zipcode", "123456");
$formData = $formBuilder->build();
```

Using the built form data is done easily by injecting it into the `MetronomeBuilder`.
```php
$envBuilder = new MetronomeBuilder(static::createClient());
$envBuilder->injectForm($formData);

$testEnv = $this->envBuilder->build();
$testEnv->post("/register");
```

You can use `injectForm` multiple times.  
The `FormFactory` mock will return the forms in the order they were injected.  
There are cases where you want to write a test specified to the second form.
To skip the first form, you can inject `MetronomeNonSubmittedForm` or `MetronomeInvalidForm` before your actual `MetronomeFormData`. 
 

## Using the Symfony Crawler
After performing a request with the `MetronomeEnvironment`, you can crawl the result using `getLatestCrawler`.   
Using the crawler, you can find specific content in the response.   
This crawler updates with every request you make.   
It directly returns the Symfony Crawler; please refer its documentation for details.   


```php
public function test_givenLoggedIn_whenGetLogsDashboard_thenShouldFindDashboard() {
    $testEnv = $this->testEnvBuilder->build();

    $testEnv->get("/admin/logs");

    $crawler = $testEnv->getLatestCrawler();
    $nameFilterResult = $crawler
        ->filter('html:contains("Dashboard")')
        ->count();
    $this->assertGreaterThan(0, $nameFilterResult);
}
```

Please note that this example uses a CSS selector, which requires the `symfony/css-selector` dependency.

## Verifying FlashBag data
The `FlashBag` in the user's session can be a convenient tool for your website.   
Metronome allows you to access the `FlashBag` through the `MetronomeEnvironment`.   
This can help you verify the outcome of your GET or POST request even better.   

In the example below, assume that the page has no log messages to show and reports this in a flash message.
```php
$testEnvBuilder = new MetronomeBuilder(static::createClient());
$testEnv = $testEnvBuilder->build();

$testEnv->get("/admin/logs");

$flash = $testEnv->getFlashBag();
$this->assertNotEmpty($flash);
```

The `getFlashBag` function returns an associative array, with an entry for every key.   
Each key entry is also an array, containing the flash messsages associated to that key.