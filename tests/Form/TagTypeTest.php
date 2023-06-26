<?php

namespace App\Tests\Forms;

use App\Entity\Tag;
use App\Form\Type\TagType;
use Symfony\Component\Form\Test\TypeTestCase;


class TagTypeTest extends TypeTestCase
{
    public function testSubmitValidDate()
    {

        $formatData = [
            'title' => 'TestTag'
        ];

        $model = new Tag();
        $form = $this->factory->create(TagType::class, $model);

        $expected = new Tag();
        $expected->setTitle('TestTag');

        $form->submit($formatData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }
}