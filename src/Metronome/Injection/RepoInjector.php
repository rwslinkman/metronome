<?php
namespace Metronome\Injection;

interface RepoInjector
{
    public function repositoryName();
    public function repositoryClass();
    public function inject();
}