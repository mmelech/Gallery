<?php

namespace App\Tests\Form;

use App\Entity\Gallery;
use App\Form\Type\GalleryType;
use Symfony\Component\Form\Test\TypeTestCase;

class GalleryTypeTest extends TypeTestCase
{

    public function testSubmitValidDate()
    {

        $formatData = [
            'title' => 'TestGallery'
        ];

        $model = new Gallery();
        $form = $this->factory->create(GalleryType::class, $model);

        $expected = new Gallery();
        $expected->setTitle('TestGallery');

        $form->submit($formatData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }

}