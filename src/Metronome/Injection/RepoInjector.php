<?php
namespace Metronome\Injection;

/**
 * Interface RepoInjector
 * @package Metronome\Injection
 *
 * This interface must be used when Metronome should inject an Doctrine EntityRepository
 */
interface RepoInjector
{
    /**
     * @return mixed Acts as an identifier for the repository
     */
    public function repositoryName();

    /**
     * @return string Full namespace for the repository to mock
     */
    public function repositoryClass();

    /**
     * @return array Key => Value array of methods to mock and their respective results
     */
    public function inject();
}