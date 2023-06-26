<?php

namespace App\Tests\Form;

use App\Entity\Tag;
use App\Form\Type\TagFiltrType;
use Symfony\Component\Form\Test\TypeTestCase;

class TagFiltrTypeTest extends TypeTestCase
{

    public function testSubmitValidDate()
    {

        $formatData = [
            'title' => 'TestTag'
        ];

        $model = new Tag();
        $form = $this->factory->create(TagFiltrType::class, $model);

        $expected = new Tag();
        $expected->setTitle('TestTag');

        $form->submit($formatData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }

}