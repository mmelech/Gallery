<?php
/**
 * Edit photo form.
 */

namespace App\Form\Type;

use App\Entity\Photo;
use App\Form\DataTransformer\TagsDataTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

/**
 * Class PhotoEditType.
 */
class PhotoEditType extends AbstractType
{
    /**
     * data transformer.
     *
     * @var TagsDataTransformer
     */
    private $tagsDataTransformer;

    /**
     * Constructor.
     *
     * @param TagsDataTransformer $tagsDataTransformer Tags Data Transformer
     */
    public function __construct(TagsDataTransformer $tagsDataTransformer)
    {
        $this->tagsDataTransformer = $tagsDataTransformer;
    }

    /**
     * Form builder.
     *
     * @param FormBuilderInterface $builder Form Builder Interface
     * @param array                $options Options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'label_title',
                    'required' => true,
                    'attr' => ['max_length' => 120],
                ]
            )
            ->add(
                'content',
                TextType::class,
                [
                    'label' => 'label.content',
                    'required' => true,
                    'attr' => ['max_length' => 65000],
                ]
            );
        $builder->add(
            'gallery',
            EntityType::class,
            [
                'label' => 'label.gallery',
                'class' => "App\Entity\Gallery",
                'placeholder' => 'label.gallery',
                'choice_label' => 'title',
            ]
        );
        $builder->add(
            'tags',
            TextType::class,
            [
                'label' => 'label_tags',
                'required' => false,
                'attr' => ['max_length' => 128],
            ]
        );
        $builder->add(
            'file',
            FileType::class,
            [
                'mapped' => false,
                'label' => 'label.photo',
                'required' => false,
                'constraints' => new Image(
                    [
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/pjpeg',
                            'image/jpeg',
                            'image/pjpeg',
                        ],
                    ]
                ),
            ]
        );
        $builder->get('tags')->addModelTransformer(
            $this->tagsDataTransformer
        );
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver Options Resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Photo::class]);
    }

    /**
     * Prefix.
     */
    public function getBlockPrefix(): string
    {
        return 'photo';
    }
}
