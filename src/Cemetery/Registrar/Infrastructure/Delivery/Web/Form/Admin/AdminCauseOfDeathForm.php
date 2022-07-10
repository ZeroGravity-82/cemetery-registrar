<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminCauseOfDeathForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
