# Metronome ![](https://travis-ci.com/rwslinkman/metronome.svg?branch=master)
**This project is deprecated and should no longer be used.**   
**Please read the DEPRECATION NOTICE for further information.** 

> Metronome is a lightweight test utility for Symfony 3 and Symfony 4 (PHP) applications.   
> It provides a steady base for easy mocking and injection of the Symfony Container.     
> Let Metronome help you orchestrate  the Symfony in your hands!      

Metronome aims to make functional testing easier for Symfony projects.   
It creates a custom `Kernel` and `Container` and injects everything you need for a fully set-up Symfony environment.   
You application is automatically loaded using Symfony's `WebTestCase` and `KernelBrowser` client.   

Metronome provides several builders to aid in your tests:   
- `MetronomeBuilder`
- `MetronomeDoctrineMockBuilder`
- `MetronomeFileSystemBuilder`

Using the Metronome you can:
- Build a `MetronomeEnvironment` that sends GET and POST requests to test your `Controller` classes
- Build a mocked `EntityManager` to test classes that have database interactions
- Build a mocked `ReferenceRepository` to test your `Fixture` classes
- Build a mocked `ManagerRegistry` to test your `EntityRepository` classes  
- Inject `MetronomeLoginData` to bypass your `GuardAuthenticator` protection
- Mock `Symfony Forms`  using the `MetronomeFormDataBuilder` and `MetronomeEntityFormDataBuilder`
- Verify the contents of the Symfony `FlashBag`

## DEPRECATION NOTICE
**This project is no longer in active development.**   
**Initially, the project was created in a time when Symfony did not fully support dependency injection for testing.**   
**Also, it was difficult to create mocks that could be used in the Symfony kernel.**   
**Testing your Symfony Controllers was pretty difficult at the time. This is no longer the case.**   

**Over the years, both Symfony and PHPUnit have seen great improvements in these parts.**   
**Metronome is therefor becoming more and more obsolete.**   
**It becomes more of a burden than a benefit for projects.**   
**Using the improved dependency injection system, testing Symfony Controllers can be done well without Metronome.**   

**In case you want to migrate away from Metronome, take a look at mocking in PHPUnit.**   
**These mocks can easily be passed in Controllers' constructors when testing.**   


## Installation
You can install Metronome using `composer` to get the package from Packagist.

```
composer require-dev rwslinkman/metronome
```

Metronome is not needed in the production environment, since it is a test utility.   
The latest version is `3.0.0`.         
Older versions may work for Symfony 3 projects, but this may prove difficult and is not supported.   
For bleeding edge development, point to the `dev-develop` or `dev-master` version.

## Usage
Metronome is used in combination with PHPUnit and the Symfony `WebTestCase`, which extends PHPUnit's `TestCase`.   

### MetronomeBuilder
With the `MetronomeBuilder`, you can create a fully set-up environment of your application.   
The connection to the database, `EntityManager`, is automatically mocked.   
You can inject your own Kernel, or use the ready-made MetronomeKernel.      
After setting this up, you inject services, repositories and other objects you need.
Calling the `build` function returns a `MetronomeEnvironment` that allows to fire `GET`, `POST`, and `PUT` to your application.   

Creating an environment for your application with no mocks (Doctrine excluded): 
```php
$clientBuilder = new MetronomeTestClientBuilder();
$builder = new MetronomeBuilder($clientBuilder->build());
$environment = $builder->build();

$response = $environment->get("/");
$this->assertEquals(200, $response->getStatusCode());
```

### Testing a Controller

Setup a `MetronomeEnvironment` using the `MetronomeBuilder`.   
The most basic test returns a `Response` from `Symfony\HttpFoundation`.   
Make sure your test extents `WebTestCase` to make use of Symfony's `Client` for HTTP requests.   

```php
class IndexControllerTest extends WebTestCase
{
    /** @var MetronomeBuilder */
    private $testEnvBuilder;
    
    public function setUp() {
        $clientBuilder = new MetronomeTestClientBuilder();
        $clientBulder->controller(IndexController::class);
        $this->testEnvBuilder = new MetronomeBuilder($clientBuilder->build());
        $this->testEnvBuilder->setupController(IndexController::class);
    }

    public function test_givenApp_whenGetIndex_thenShouldReturnOK() {
        /** @var MetronomeEnvironment */
        $environment = $builder->build();
        
        /** @var Response */
        $response = $environment->get("/");
        
        $this->assertEquals(200, $response->getStatusCode());
    }
}
```

If you have custom services configured in Symfony's `services.yaml`, then you can mock those during tests.   
This is convenient when testing `Controllers`.
You can make use of the `MetronomeDynamicMockBuilder` that easily mocks classes.   

```php
class IndexControllerTest extends WebTestCase
{
    /** @var MetronomeBuilder */
    private $testEnvBuilder;
    
    public function setUp() {
        $clientBuilder = new MetronomeTestClientBuilder();
        $clientBulder->controller(IndexController::class);
        $this->testEnvBuilder = new MetronomeBuilder($clientBuilder->build());
        $this->testEnvBuilder->setupController(IndexController::class);
    }
    
    public function test_givenApp_whenGetIndex_andEmptyList_thenShouldReturnOK() {
        $mockBuilder = new MetronomeDynamicMockBuilder(UserService::class);
        $mockBuilder->method("getUsers", array());
        $this->testEnvBuilder->injectObject("myapp.user_service", $mockBuilder->build());
        
        /** @var MetronomeEnvironment */
        $testEnv = $this->testEnvBuilder->build();

        /** @var Response */
        $result = $testEnv->get("/");
        
        $this->assertEquals(200, $result->getStatusCode());
    }
}
```

For more static components that you inject as services, you can use `ServiceInjector` to return basic values.  
Optionally, add some setters to set your desired outcome for the mocked function.   
`MetronomeBuilder` has a special `injectService()` function to accept these type of injections.
   
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
}
```

### Arguments and Definitions
When testing `Controllers`, you will need to deal with constructor arguments that are injected by Symfony.   
Metronome provides the `MetronomeArgument` that injects a mock directory into the Controller contructor when Symfony needs it.   
Provide the parameter name and the service you want to inject and call `setupController` on your `MetronomeBuilder`.
Metronome provides a small catalog with premade Arguments.      

```php
$clientBuilder = new MetronomeTestClientBuilder();
$clientBuilder->controller(ProjectController::class);
$this->builder = new MetronomeBuilder($clientBuilder->build());
$this->builder->setupController(ProjectController::class, array(
    new MetronomeServiceArgument("projectsService", "my_app.projects_service"),
    new MetronomeFormFactoryArgument("formFactory"),
    new MetronomeSessionArgument("session")
));
```

Note: the services must be injected in the Container when building a `MetronomeEnvironment`.   

Symfony might want to load system services earlier than calling the Controller.   
In that case, add a `MetronomeDefinition` to the test client:
```
$clientBuilder = new MetronomeTestClientBuilder();
$clientBuilder->controller(ProjectController::class);
$clientBuilder->addDefinition(new MetronomeDefinition(TokenStorage::class, TokenStorageInterface::class));
$clientBuilder->addDefinition(new MetronomeDefinition(AuthenticationUtils::class));
```

### Mocking the database and EntityManager
Classes that use the `Doctrine EntityManager` can be tested using Metronome.   
These are usually the services in your Symfony application.   
You can inject `RepoInjector` classes to mock your `EntityRepository`.   
 
```php
$metronomeBuilder = new MetronomeDoctrineMockBuilder();
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

### Testing Fixtures
Fixtures are part of the `Doctrine FixtureBundle`.   
Metronome can be used to verify some of the `Fixture` behaviour.

```php
use PHPUnit\Framework\TestCase;

class MyFixtureTest extends TestCase
{
    public function test_givenFixture_whenLoad_shouldAlwaysPersist() {
        $envBuilder = new MetronomeDoctrineMockBuilder();
        $mockEm = $envBuilder->buildEntityManager();

        $fixture = new MyFixture();
        $fixture->load($mockEm);

        $mockEm->shouldHaveReceived("flush");
    }
}
```

### Bypassing the Guard authentication system
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
        $clientBuilder = new MetronomeTestClientBuilder();
        $clientBulder->controller(AdminController::class);
        $this->testEnvBuilder = new MetronomeBuilder($clientBuilder->build());
        $this->testEnvBuilder->setupController(AdminController::class);
        
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


### Testing forms in your Controllers
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
$clientBuilder = new MetronomeTestClientBuilder();
$testEnvBuilder = new MetronomeBuilder($clientBuilder->build());
$envBuilder->injectForm($formData);

$testEnv = $this->envBuilder->build();
$testEnv->post("/register");
```

You can use `injectForm` multiple times.  
The `FormFactory` mock will return the forms in the order they were injected.  
There are cases where you want to write a test specified to the second form.
To skip the first form, you can inject `MetronomeNonSubmittedForm` or `MetronomeInvalidForm` before your actual `MetronomeFormData`. 

Please note that this example uses a CSS selector, which requires the `symfony/css-selector` dependency.

### Verifying FlashBag data
The `FlashBag` in the user's session can be a convenient tool for your website.   
Metronome allows you to access the `FlashBag` through the `MetronomeEnvironment`.   
This can help you verify the outcome of your GET or POST request even better.   

In the example below, assume that the page has no log messages to show and reports this in a flash message.
```php
$clientBuilder = new MetronomeTestClientBuilder();
$testEnvBuilder = new MetronomeBuilder($clientBuilder->build());
$testEnv = $testEnvBuilder->build();

$testEnv->get("/admin/logs");

$flash = $testEnv->getFlashBag();
$this->assertNotEmpty($flash);
```

The `getFlashBag` function returns an associative array, with an entry for every key.   
Each key entry is also an array, containing the flash messsages associated to that key.