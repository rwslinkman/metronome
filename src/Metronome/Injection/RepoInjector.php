<?php
namespace JappserBundle\Tests\TestEnvironment\RepoInjector;

interface RepoInjector
{
    public function repositoryName();
    public function repositoryClass();
    public function inject();
}