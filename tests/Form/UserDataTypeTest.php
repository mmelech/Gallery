<?php

namespace App\Tests\Form;

use App\Entity\UserData;
use App\Form\Type\UserDataType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserDataTypeTest extends TypeTestCase
{

    public function testSubmitValidDate()
    {

        $formatData = [
            'firstname' => 'TestFirstName',
            'login' => 'TestLogin'
        ];

        $model = new UserData();
        $form = $this->factory->create(UserDataType::class, $model);

        $expected = new UserData();
        $expected->setFirstname('TestFirstName');
        $expected->setLogin('TestLogin');

        $form->submit($formatData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }

}