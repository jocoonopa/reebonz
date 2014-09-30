<?php

namespace Woojin\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApiController extends Controller
{
    public function getSerializer() 
    {      
        return \JMS\Serializer\SerializerBuilder::create()->build();
    }
}