<?php

namespace Mrapps\OnesignalBundle\Subscriber;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class TableSuffixSubscriber implements EventSubscriber
{
    protected $suffix = '';

    public function __construct($container)
    {
        if($container !== null) {
            $this->suffix = ($container->hasParameter('mrapps_onesignal_table_suffix')) ? $container->getParameter('mrapps_onesignal_table_suffix') : '';
        }
    }

    public function getSubscribedEvents()
    {
        return array('loadClassMetadata');
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $classMetadata = $args->getClassMetadata();
        if ($classMetadata->isInheritanceTypeSingleTable() && !$classMetadata->isRootEntity()) {
            // if we are in an inheritance hierarchy, only apply this once
            return;
        }

        $classMetadata->setTableName($classMetadata->getTableName().'_'.$this->suffix);

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if ($mapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
                $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $mappedTableName.'_'.$this->suffix;
            }
        }
    }
}