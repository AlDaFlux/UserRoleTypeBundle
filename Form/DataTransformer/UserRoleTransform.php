<?php
namespace Aldaflux\AldafluxUserRoleTypeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class UserRoleTransform implements DataTransformerInterface
{
    
    /**
     * {@inheritdoc}
     */
    public function transform($array)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($array)
    { 
        $returnArray = [];
        foreach ($array as $key => $value) 
        {
            if (substr($key, 0,12)=="___ADD_ROLE_")
            {
                $returnArray[] = $value;
            }
            elseif ($value) {
                $returnArray[] = $key;
            }
        }
        return $returnArray;
    }
}

