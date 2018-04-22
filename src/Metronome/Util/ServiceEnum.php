<?php
namespace Metronome\Util;


abstract class ServiceEnum
{
    const SECURITY_TOKEN_STORAGE    = 'security.token_storage';
    const SECURITY_AUTH_UTILS       = 'security.authentication_utils';
    const TEMPLATING                = "templating";
    const FORM_FACTORY              = "form.factory";
    const ENTITY_MANAGER            = 'doctrine.orm.entity_manager';
}