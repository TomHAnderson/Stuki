<?php
namespace DoctrineModuleTest\Authentication\Adapter;
use DoctrineModuleTest\Framework\TestCase;

class DoctrineEntityTest extends TestCase 
{
    public function setUp()
    {
        $this->createDb();
    }
    
    public function testInvalidLogin()
    {
        $em = $this->getEntityManager();
        
        $adapter = new \DoctrineModule\Authentication\Adapter\DoctrineEntity(
            $em,
            'DoctrineModuleTest\Assets\Entity\Test'
        );
        $adapter->setIdentity('username');
        $adapter->setCredential('password');
        
        $result = $adapter->authenticate();
        
        $this->assertFalse($result->isValid());
    }
    
    public function testValidLogin()
    {
        $em = $this->getEntityManager();
        
        $entity = new \DoctrineModuleTest\Assets\Entity\Test;
        $entity->username = 'username';
        $entity->password = 'password';
        $em->persist($entity);
        $em->flush();
        
        $adapter = new \DoctrineModule\Authentication\Adapter\DoctrineEntity(
            $em,
            'DoctrineModuleTest\Assets\Entity\Test'
        );
        $adapter->setIdentity('username');
        $adapter->setCredential('password');
        
        $result = $adapter->authenticate();
        
        $this->assertTrue($result->isValid());
    }
}
