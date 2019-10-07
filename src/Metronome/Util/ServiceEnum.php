<?php
namespace Metronome\Util;

abstract class ServiceEnum
{
    const SECURITY_TOKEN_STORAGE    = 'security.token_storage';
    const SECURITY_AUTH_UTILS       = 'security.authentication_utils';
    const FORM_FACTORY              = "form.factory";
    const ENTITY_MANAGER            = 'doctrine.orm.entity_manager';
    const DEFAULT_ENTITY_MANAGER    = "doctrine.orm.default_entity_manager";
    const TWIG                      = "twig";
    const SESSION                   = "session";
}