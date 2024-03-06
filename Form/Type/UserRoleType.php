<?php
namespace Aldaflux\AldafluxUserRoleTypeBundle\Form\Type;


use Aldaflux\AldafluxUserRoleTypeBundle\Form\DataTransformer\UserRoleTransform;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver; 

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Aldaflux\AldafluxUserRoleTypeBundle\DataCollector\UserRoleTypeCollector;

/**
 * Role form
 *
 * @author Lescallier Rémy <lescallier1@gmail.com>
 */
class UserRoleType extends AbstractType
{
    private $roleHierarchy;
    private $roles;
    private $rolesToKeep;
    
    private $security;
    private $user;
    private $reachableRoles;

    private $display;
    
    private $profiles;
    private $profile;
    
    private $configs;
    private $config;
    private $configName;
    
    private $hidden_default_role;
    
    private $label;
    private $collector;
    
    private $options;
    
    

    public function __construct(RoleHierarchyInterface $roleHierarchy , Security $security, ParameterBagInterface $parameters, UserRoleTypeCollector $collector)
    {
        
        $this->roleHierarchy = $roleHierarchy;
        $this->parameters = $parameters;
        $this->security= $security;
        $this->user = $this->security->getUser();
        $this->roles = array();
        $this->rolesToKeep = array();
        
        
        $this->collector=$collector;
        
        
        $this->profiles=$parameters->Get("aldaflux_user_role_type.profiles");
        $this->configs=$parameters->Get("aldaflux_user_role_type.configs");
        $this->label=$parameters->Get("aldaflux_user_role_type.label");
        
        
        
        if ($this->user)
        {
            $this->reachableRoles = $this->roleHierarchy->getReachableRoleNames($this->user->getRoles());
        }
        else
        {   
            $this->reachableRoles =[];
        }
    }
    
    
    public function ConfigOrOptions($optionName)
    {
        
        if (isset($this->options[$optionName]) && $this->options[$optionName])
        {
            $this->{$optionName}=$this->options[$optionName];
        }
        elseif(isset($this->config[$optionName]))
        {
            $this->{$optionName}=$this->config[$optionName];
        }
        $this->collector->setData($optionName,$this->{$optionName}); 
    }

        
    

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
            $this->options=$options;
                    
            
            $this->config=$this->configs[$options["config"]];
            $this->configName=$options["config"];
            
             

            $this->collector->setData('config_name',$this->configName);
            $this->collector->setData('config',$this->config);
            
            $this->configOrOptions("display");
            $this->configOrOptions("profile");
            
            $this->configOrOptions("security_checked");
            $this->configOrOptions("hidden_default_role");
             

        
            if ($this->profile)
            {
                if (substr($this->profile, 0,5)=="ROLE_")
                {
                    if (isset($this->parameters->Get('security.role_hierarchy.roles')[$this->profile]))
                    {
                        $this->roles=$this->parameters->Get('security.role_hierarchy.roles')[$this->profile];
                    }
                }
                elseif (isset($this->profiles[$this->profile]))
                {
                    $this->roles=[];
                    foreach ($this->profiles[$this->profile] as $role)
                    {
                        $this->roles[$role] = $role;
                    }
                }
            }
            else
            {
                foreach ($this->parameters->Get('security.role_hierarchy.roles') as $key => $value) {
                    $this->roles[$key] = $key;
                    foreach ($value as $value2) {
                        $this->roles[$value2] = $value2;
                    }
                }        
                $this->roles = array_unique($this->roles);        

                if (! $this->roles)
                {
                 $this->roles = [
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_ADMIN' => 'ROLE_ADMIN'
                    ];
                }

       
             
                
                
                
            }
            
            
            $this->collector->setData('roles',$this->roles);
            
               
                         
            
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) 
            {
                
            $form = $event->getForm();
            $formOptions = $form->getConfig()->getOptions();
            
            $permissionData = $event->getData();
            
            $roles=$this->labelFormatter($this->roles);


            if ($this->hidden_default_role)
            {
                $form->add("___ADD_ROLE_".$this->hidden_default_role,HiddenType::class , ['data' => $this->hidden_default_role]);
            }
            

            $this->collector->setData('reachable_roles',$this->reachableRoles);
            $this->collector->setData('roles_formated',$roles);
             

            foreach ($roles as $label => $role) 
            {
                
                $options = ['required' => false,'label' => $label];

                if (in_array($role, $permissionData)) {
                    $options['attr']['checked'] = true;
                }
                
                if (in_array($role, $this->reachableRoles))
                {
                    $form->add($role, $formOptions['input_type'], $options);
                }
                else
                {
                    
                    if ($this->security_checked)
                    {
                        $options['disabled'] = true;
                        if ($this->display=="all")
                        {
                            $form->add($role, $formOptions['input_type'], $options);
                        }
 
                        if (in_array($role, $permissionData)) 
                        {
                            if ($this->display=="standard")
                            {
                                $form->add($role, $formOptions['input_type'], $options);
                            }
                            $form->add("___ADD_ROLE_".$role,HiddenType::class , ['data' => $role]);
                        }
                    }
                    else
                    {
                        if ($this->display=="all")
                        {
                            $form->add($role, $formOptions['input_type'], $options);
                        }
                        
                    }
                }
                
            }
        });
        $builder->addModelTransformer(new UserRoleTransform());
    }

    
    function labelFormatter($roles)
    {
        $rolesReponse=array();
        
        
        foreach ($roles as $role)
        {
            
            if ($this->label["display"]=='word')
            {
                $key=ucwords(strtolower(str_replace("_", " ",$role)));
                if (substr($key, 0, 5)=="Role ")
                {
                    $key=substr($key, 5);
                }
            }
            elseif ($this->label["display"]=='traduction')
            {
                $key=$this->label["translation_prefixe"].strtolower($role);
            }
            else 
            {
                $key=$role;
            }
            
            
            $rolesReponse[$key]=$role;
        }
        return($rolesReponse);
    }
    
    
    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'input_type' => CheckboxType::class,
            'config' => "default",
        ]);
        /*
        $resolver->setDefaults([
            'security_checked' => true,
            'input_type' => CheckboxType::class,
            'profile' => null,
        ]);
         * 
         */
    }
}
