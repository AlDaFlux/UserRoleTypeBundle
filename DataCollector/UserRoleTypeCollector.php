<?php

namespace Aldaflux\AldafluxUserRoleTypeBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;


class UserRoleTypeCollector extends AbstractDataCollector{

    
   // public  $data;
    public  $reachableRoles;
    
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
        $this->reachableRoles = [];
    }

    
    
//    Response $response
            
    public function collect(Request $request, Response $response, \Throwable $exception = null) : void
    {
    //        $this->data = ['test' => "OK"];
    }
    
    
    public function getData()
    {
        return($this->data);
    }
    
    public function setData($key, $value)
    {
        $this->data[$key]=$value;
    }
    
    
    
    public function getRoles()
    {
        return($this->data['roles']);
    }
    
    public function getRolesFormated()
    {          
        return($this->data['roles_formated']);
    }
    
    
    public function getReachableRoles()
    {          
       //return($this->reachableRoles);
       return($this->data['reachable_roles']);
    }
    
    public function getConfigName()
    {          
       //return($this->reachableRoles);
       return($this->data['config_name']);
    }
    
    
    public function getConfig()
    {          
       return($this->data['config']);
    }
    public function getProfile()
    {          
       return($this->data['profile']);
    }
    
    public function getSecurityChecked()
    {          
       return($this->data['security_checked']);
    }
    
    public function getHiddenDefaultRole()
    {          
       return($this->data['hidden_default_role']);
    }
    
    public function getDisplay()
    {          
       return($this->data['display']);
    }
    
    
    
    
    
    
    
    
    

    public static function getTemplate(): ?string
    {
        return '@AldafluxUserRoleType/data_collector/user_role_type_collector.html.twig';
    }
    
    
    
 
    
    
    
    
}