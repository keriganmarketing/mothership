<?php

namespace App;

use PHRETS\Session;
use PHRETS\Configuration;
use Illuminate\Database\Eloquent\Model;

class ApiCall extends Model
{

    /**
     * @return string
     */
    private function ecarLoginUrl()
    {
        return 'http://retsgw.flexmls.com/rets2_2/Login';
    }

    /**
     * @return string
     */
    private function ecarUserName()
    {
        return config('app.ecar_username');
    }

    /**
     * @return string
     */
    private function ecarPassword()
    {
        return config('app.ecar_password');
    }

    /**
     * @return string
     */
    private function bcarLoginUrl()
    {
        return 'http://retsgw.flexmls.com:80/rets2_3/Login';
    }

    /**
     * @return string
     */
    private function bcarUserName()
    {
        return config('app.bcar_username');
    }

    /**
     * @return string
     */
    private function bcarPassword()
    {
        return config('app.bcar_password');
    }

    /**
     * @return PHRETS\Session
     */
    public function loginToBcar()
    {
        $config = new Configuration();
        $config->setLoginUrl($this->bcarLoginUrl())
               ->setUsername($this->bcarUserName())
               ->setPassword($this->bcarPassword())
               ->setRetsVersion('1.7.2')
               ->setOption("compression_enabled", true)
               ->setOption("offset_support", true);

        $rets = new Session($config);

        $rets->Login();

        return $rets;
    }

    /**
     * @return PHRETS\Session
     */
    public function loginToEcar()
    {
        $config = new Configuration();
        $config->setLoginUrl($this->ecarLoginUrl())
               ->setUsername($this->ecarUserName())
               ->setPassword($this->ecarPassword())
               ->setRetsVersion('1.7.2')
               ->setOption("compression_enabled", true)
               ->setOption("offset_support", true);


        $rets = new Session($config);

        $rets->Login();

        return $rets;
    }
}
