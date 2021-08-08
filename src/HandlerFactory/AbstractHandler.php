<?php

namespace App\HandlerFactory;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractHandler
 * @package App\HandlerFactory
 */
abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected FormFactoryInterface $formFactory;

    /**
     * @var FormInterface
     */
    protected FormInterface $form;

    /**
     * @param FormFactoryInterface $formFactory
     * @required
     */
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param $data
     * @param array $options
     */
    abstract protected function process($data, array $options): void;

    /**
     * @param OptionsResolver $resolver
     */
    protected function configure(OptionsResolver $resolver): void
    {
    }

    /**
     * @param Request $request
     * @param null $data
     * @param array $options
     * @return bool
     */
    public function handle(Request $request, $data = null, array $options = []): bool
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired("form_type");
        $resolver->setDefault("form_options", []);

        $this->configure($resolver);

        $options = $resolver->resolve($options);

        $this->form = $this->formFactory
            ->create($options["form_type"], $data, $options["form_options"])
            ->handleRequest($request)
        ;

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $this->process($data, $options);
            return true;
        }

        return false;
    }

    /**
     * @return FormView
     */
    public function createView(): FormView
    {
        return $this->form->createView();
    }
}
