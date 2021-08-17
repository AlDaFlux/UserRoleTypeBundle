<?php

namespace Aldaflux\AldafluxUserRoleTypeBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;


class UserRoleTypeCollector extends AbstractDataCollector{

    public function __construct()
    {
    
    }
    
    
    public function getName() : string
    {
        return 'aldaflux.user_role_type_collector';
    }
    
    
     public function reset(): void
    {
        $this->data = [];
    }

    
    
//    Response $response
            
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
            $this->data = ['test' => "OK"];
    }
    
    

    public static function getTemplate(): ?string
    {
        return '@AldafluxUserRoleType/data_collector/user_role_type_collector.html.twig';
    }
    
    
    
 
    
    
    
    
}