<?php

class ComPeopleAuthenticationResponse extends AnObject
{
    public $status = ComPeopleAuthentication::STATUS_FAILURE;
    public $type = '';
    public $error_message = '';
    public $username = '';
    public $email = '';
    public $password = '';
}
