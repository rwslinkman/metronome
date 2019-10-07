<?php
namespace Metronome\Injection\Mocking;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Metronome\Form\MetronomeFormData;
use Mockery\MockInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
     * @return EntityManager|MockInterface
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
        $connMock = MockCreator::mock('\Doctrine\DBAL\Connection', array(
            'exec' => null
        ));

        $qbMock = MockCreator::mock('\Doctrine\ORM\QueryBuilder', array(
            'expr' => new Expr(),
            'select' => null
        ));

        $emMock = MockCreator::mock('\Doctrine\ORM\EntityManager', array(
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
        $bep = MockCreator::mock('\Doctrine\ORM\Persisters\Entity\BasicEntityPersister', array(
            "load" => $load,
            "loadAll" => $loadAll
        ));

        $uowMock = MockCreator::mock('\Doctrine\ORM\UnitOfWork', array(
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
        $userProviderMock = MockCreator::mock('\Metronome\Injection\MetronomeAuthenticator',
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
     * @return AuthenticationUtils|MockInterface
     */
    public static function createAuthUtilsMock($getLastAuthenticationError) {
        $utilMock = MockCreator::mock('Symfony\Component\Security\Http\Authentication\AuthenticationUtils', array(
            "getLastAuthenticationError" => $getLastAuthenticationError
        ));
        return $utilMock;
    }

    /**
     * @param $injectedForms
     * @return FormFactoryInterface|MockInterface
     */
    public static function createFormFactoryMock($injectedForms) {
        $mockForms = array();
        /** @var MetronomeFormData $injectedForm */
        foreach($injectedForms as $injectedForm) {
            $mockForm = self::createFormMock($injectedForm->isValid(), $injectedForm->getSubmittedData(), $injectedForm->getErrors());
            array_push($mockForms, $mockForm);
        }

        $builderMock = MockCreator::mock('\Symfony\Component\Form\FormBuilderInterface', array(
            'getForm' => $mockForms[0]
        ));

        $ffMock = MockCreator::mock(FormFactoryInterface::class, array(
            "createNamedBuilder" => $builderMock
        ));

        $ffMock->allows("create")->andReturnValues($mockForms);
        return $ffMock;
    }

    public static function createTwigEnvironment() {
        $html = "Test environment prevented template of being rendered";
        $templateMock = MockCreator::mock('\Twig\Template', array(
            "render" => $html
        ));
        $loaderMock = MockCreator::mock('Twig\Loader\LoaderInterface', array(
            "exists" => true
        ));

        $twigMock = MockCreator::mock('\Twig\Environment', array(
            "disableDebug" => null,
            "getLoader" => $loaderMock,
            "loadTemplate" => $templateMock,
            "render" => $html
        ));
        return $twigMock;
    }

    public static function createTokenStorageMock(PostAuthenticationGuardToken $token = null) {
        $storageMock = MockCreator::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage', array(
            'getToken' => $token,
            'setToken' => null
        ));
        return $storageMock;
    }

    private static function createFormMock($isValid, $getData, $errors) {
        $formMock = MockCreator::mock('\Symfony\Component\Form\Form', array(
            "handleRequest" => null,
            "isSubmitted" => true,
            "getData" => $getData,
            "createView" => null,
            "isValid" => $isValid,
            "getErrors" => $errors
        ));
        return $formMock;
    }
}
