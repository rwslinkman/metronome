<?php
namespace Metronome\Injection;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Metronome\Form\MetronomeFormData;
use Mockery\MockInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig_Environment;

class MockBuilder
{
    /**
     * @param null $getRepository
     * @param null $persist
     * @param null $flush
     * @param null $clear
     * @param null $load
     * @param null $loadAll
     * @param null $entityName
     * @return EntityManager|\Mockery\MockInterface
     */
    public static function createMockEntityManager(
        $getRepository = null,
        $persist = null,
        $flush = null,
        $clear = null,
        $load = null,
        $loadAll = null,
        $entityName = null)
    {
        //
        $connMock = \Mockery::mock('\Doctrine\DBAL\Connection', array(
            'exec' => null
        ));

        $qbMock = \Mockery::mock('\Doctrine\ORM\QueryBuilder', array(
            'expr' => new Expr(),
            'select' => null
        ));

        $emMock = \Mockery::mock('\Doctrine\ORM\EntityManager', array(
            'getRepository' => $getRepository,
            'persist' => $persist,
            'flush' => $flush,
            'clear' => $clear,
            'getUnitOfWork' => self::createMockUnitOfWork($load, $loadAll),
            'createQueryBuilder' => $qbMock,
            'remove' => null,
            'getClassMetadata' => new ClassMetadata($entityName),
            'getConnection' => $connMock
        ));
        return $emMock;
    }

    private static function createMockUnitOfWork($load = null, $loadAll = null) {
        $bep = \Mockery::mock('\Doctrine\ORM\Persisters\Entity\BasicEntityPersister', array(
            "load" => $load,
            "loadAll" => $loadAll
        ));

        $uowMock = \Mockery::mock('\Doctrine\ORM\UnitOfWork', array(
            "__construct" => null,
            "getEntityPersister" => $bep,
        ));
        return $uowMock;
    }

    /**
     * @param PostAuthenticationGuardToken $token
     * @return MockInterface|AbstractGuardAuthenticator
     */
    public static function createMockUserProvider(PostAuthenticationGuardToken $token) {
        $userProviderMock = \Mockery::mock('\Metronome\Injection\MetronomeAuthenticator',
            array(
                'getUser' => $token->getUser(),
                'getCredentials' => (object)array('token' => 'aToken'),
                'onAuthenticationFailure' => (object)array('message' => 'mockErrorMessage'),
                'checkCredentials' => true,
                'createAuthenticatedToken' => $token,
                'onAuthenticationSuccess' => null,
                'supports' => true,
            ));
        return $userProviderMock;
    }

    /**
     * @param $getLastAuthenticationError
     * @return AuthenticationUtils|\Mockery\MockInterface
     */
    public static function createAuthUtilsMock($getLastAuthenticationError) {
        $utilMock = \Mockery::mock('Symfony\Component\Security\Http\Authentication\AuthenticationUtils', array(
            "getLastAuthenticationError" => $getLastAuthenticationError
        ));
        return $utilMock;
    }

    public static function createFormBuilderMock($isSubmitted = false, $isValid = false, $getData = array(), $errors = array()) {
        $formMock = \Mockery::mock('\Symfony\Component\Form\Form', array(
            "handleRequest" => null,
            "isSubmitted" => $isSubmitted,
            "getData" => $getData,
            "createView" => null,
            "isValid" => $isValid,
            "getErrors" => $errors
        ));
        $builderMock = \Mockery::mock('\Symfony\Component\Form\FormBuilderInterface', array(
            'getForm' => $formMock
        ));

        $fbMock = \Mockery::mock('\Symfony\Component\Form\FormFactory', array(
            "create" => $formMock,
            "createNamedBuilder" => $builderMock
        ));
        return $fbMock;
    }

    /**
     * @param $injectedForms
     * @return FormFactory|MockInterface
     */
    public static function createFormFactoryMock($injectedForms) {
        $mockForms = array();
        /** @var MetronomeFormData $injectedForm */
        foreach($injectedForms as $injectedForm) {
            $mockForm = self::createFormMock($injectedForm->isValid(), $injectedForm->getSubmittedData(), $injectedForm->getErrors());
            array_push($mockForms, $mockForm);
        }

        $builderMock = \Mockery::mock('\Symfony\Component\Form\FormBuilderInterface', array(
            'getForm' => $mockForms[0]
        ));

        $ffMock = \Mockery::mock(FormFactory::class, array(
            "createNamedBuilder" => $builderMock
        ));
        foreach($mockForms as $mf) {
            $ffMock->allows("create")->andReturn($mf);
        }
        return $ffMock;
    }

    private static function createFormMock($isValid, $getData, $errors) {
        $formMock = \Mockery::mock('\Symfony\Component\Form\Form', array(
            "handleRequest" => null,
            "isSubmitted" => true,
            "getData" => $getData,
            "createView" => null,
            "isValid" => $isValid,
            "getErrors" => $errors
        ));
        return $formMock;
    }

    public static function createTwigTemplatingMock(){
        $twigMock = \Mockery::mock('\Symfony\Bundle\TwigBundle\TwigEngine', array(
            "renderResponse" => new Response(""),
            "render" => ""
        ));
        return $twigMock;
    }

    /**
     * @deprecated
     * @return MockInterface|Twig_Environment
     * TODO: Improve this rendering method
     */
    public static function createTwigMock() {
        $html = "Test environment prevented template of being rendered";
        $templateMock = \Mockery::mock('\Twig_Template', array(
            "render" => $html
        ));
        $loaderMock = \Mockery::mock('\Twig_LoaderInterface', array(
            "exists" => true
        ));

        $twigMock = \Mockery::mock('\Twig_Environment', array(
            "disableDebug" => null,
            "getLoader" => $loaderMock,
            "loadTemplate" => $templateMock,
            "render" => $html
        ));
        return $twigMock;
    }

    public static function createRouterMock() {
        $routerMock = \Mockery::mock('\Symfony\Component\Routing\Router', array(
            "getRouteCollection" => array(),
            "generate" => "http://some/url",
        ));
        return $routerMock;
    }

    public static function createMockFileSystem($exists = true)
    {
        $mockFS = \Mockery::mock('\Symfony\Component\Filesystem\Filesystem', array(
            "exists" => $exists,
            "mkdir" => null,
        ));
        return $mockFS;
    }

    public static function createTokenStorageMock(PostAuthenticationGuardToken $token) {
        $storageMock = \Mockery::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage', array(
            'getToken' => $token,
            'setToken' => null
        ));
        return $storageMock;
    }
}
