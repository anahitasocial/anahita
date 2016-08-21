<?php

class ComPeopleAuthenticationResponse extends KObject
{
    public $status = ComPeopleAuthentication::STATUS_FAILURE;
    public $type = '';
    public $error_message = '';
    public $username = '';
    public $email = '';
    public $password = '';
}
