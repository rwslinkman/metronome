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
Documentation coming soon

## Bypassing the Guard authentication system
Documentation coming soon

## Testing forms in your Controllers
Documentation coming soon

## Using the Symfony Crawler
Documentation coming soon