<?php

namespace App;

use PHRETS\Session;
use PHRETS\Configuration;
use Illuminate\Database\Eloquent\Model;

class ApiCall extends Model
{
    protected $association;

    public function __construct($association)
    {
        $this->association = $association;
        $this->loginUrl    = $this->association == 'bcar' ? $this->bcarLoginUrl() : $this->ecarLoginUrl();
        $this->username    = $this->association == 'bcar' ? $this->bcarUserName() : $this->ecarUserName();
        $this->password    = $this->association == 'bcar' ? $this->bcarPassword() : $this->ecarPassword();
    }

    public function login()
    {
        $config = new Configuration();
        $config->setLoginUrl($this->loginUrl)
            ->setUsername($this->username)
            ->setPassword($this->password)
            ->setRetsVersion('1.7.2')
            ->setOption("compression_enabled", true)
            ->setOption("connect_timeout", 3400)
            ->setOption("offset_support", true);

        $rets = new Session($config);

        $rets->Login();

        return $rets;
    }

    /**
     * Returns the ECAR Login URL
     *
     * @return string
     */
    private function ecarLoginUrl()
    {
        return 'http://retsgw.flexmls.com/rets2_3/Login';
    }

    /**
     * Returns the ECAR Username specified in the config file
     *
     * @return string
     */
    private function ecarUserName()
    {
        return config('app.ecar_username');
    }

    /**
     * Returns the ECAR password specified in the config file
     *
     * @return string
     */
    private function ecarPassword()
    {
        return config('app.ecar_password');
    }

    /**
     * Returns the BCAR Login URL specified in the config file
     *
     * @return string
     */
    private function bcarLoginUrl()
    {
        return 'http://retsgw.flexmls.com:80/rets2_3/Login';
    }

    /**
     * Returns the BCAR username specified in the config file
     *
     * @return string
     */
    private function bcarUserName()
    {
        return config('app.bcar_username');
    }

    /**
     * Returns the BCAR Password specified in the config file
     *
     * @return string
     */
    private function bcarPassword()
    {
        return config('app.bcar_password');
    }
}
