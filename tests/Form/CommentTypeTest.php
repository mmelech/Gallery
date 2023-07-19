<?php

namespace App\Tests\Form;

use App\Entity\Comment;
use App\Form\Type\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;

class CommentTypeTest extends TypeTestCase
{

    public function testSubmitValidDate()
    {

        $formatData = [
            'content' => 'TestComment'
        ];

        $model = new Comment();
        $form = $this->factory->create(CommentType::class, $model);

        $expected = new Comment();
        $expected->setContent('TestComment');

        $form->submit($formatData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }

}