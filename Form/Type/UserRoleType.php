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
use Symfony\Component\Security\Core\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;



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
    
    private $label;
    
    

    public function __construct(RoleHierarchyInterface $roleHierarchy , Security $security, ParameterBagInterface $parameters)
    {
        
        $this->roleHierarchy = $roleHierarchy;
        $this->parameters = $parameters;
        $this->security= $security;
        $this->user = $this->security->getUser();
        $this->roles = array();
        $this->rolesToKeep = array();
        
        $this->profiles=$parameters->Get("aldaflux_user_role_type.profiles");
        $this->configs=$parameters->Get("aldaflux_user_role_type.configs");
        $this->label=$parameters->Get("aldaflux_user_role_type.label");
        
        
        
        
        $this->reachableRoles = $this->roleHierarchy->getReachableRoleNames($this->user->getRoles());
    }
    
    

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
            $this->config=$this->configs[$options["config"]];
            
            
            $this->display=$this->config["display"];
            
            if ($options["profile"])
            {
                $this->profile=$options["profile"];
            }
            elseif(isset($this->config["profile"]))
            {
                $this->profile=$this->config["profile"];
            }
        
        

        
            if ($this->profile)
            {
                
                if (isset($this->profiles[$this->profile]))
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
            
                         
                
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) 
            {
            $form = $event->getForm();
            $formOptions = $form->getConfig()->getOptions();
            $permissionData = $event->getData();
            
            $roles=$this->labelFormatter($this->roles);
            
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
                    if ($formOptions['security_checked'] === 'strict')
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
            'security_checked' => 'strict',
            'input_type' => CheckboxType::class,
            'profile' => null,
            'config' => "default",
        ]);
    }
}
