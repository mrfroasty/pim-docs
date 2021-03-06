<?php

namespace Acme\Bundle\IcecatDemoBundle\Filter\ORM;

use Symfony\Component\Form\FormFactoryInterface;
use Oro\Bundle\FilterBundle\Filter\ChoiceFilter;
use Oro\Bundle\FilterBundle\Datasource\FilterDatasourceAdapterInterface;
use Pim\Bundle\FilterBundle\Filter\Flexible\FilterUtility;
use Pim\Bundle\CustomEntityBundle\Form\CustomEntityFilterType;
use Acme\Bundle\IcecatDemoBundle\Manager\VendorManager;

class VendorFilter extends ChoiceFilter
{
    protected $manager;

    public function __construct(FormFactoryInterface $factory, FilterUtility $util, VendorManager $manager)
    {
        $this->formFactory = $factory;
        $this->util        = $util;
        $this->manager     = $manager;
    }

    public function apply(FilterDatasourceAdapterInterface $ds, $value)
    {
        $queryBuilder = $ds->getQueryBuilder();
        if (isset($value['value'])) {
            $alias = current($queryBuilder->getRootAliases());
            $queryBuilder
                ->innerJoin($alias.'.values', 'FilterVendorValue', 'WITH', 'FilterVendorValue.vendor IN (:vendor)')
                ->setParameter('vendor', $value['value']);
        }
    }

    public function getForm()
    {
        $options = array_merge(
            $this->getOr('options', []),
            ['csrf_protection' => false]
        );

        $options['field_options']            = isset($options['field_options']) ? $options['field_options'] : [];
        $options['field_options']['choices'] = $this->manager->getVendorChoices();

        if (!$this->form) {
            $this->form = $this->formFactory->create($this->getFormType(), [], $options);
        }

        return $this->form;
    }

    public function getMetadata()
    {
        $metadata = parent::getMetadata();
        $metadata['choices'] = $this->manager->getVendorChoices();

        return $metadata;
    }
}
