<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Property Factory.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnDomainProperty extends KObject
{
    /**
     * Attribute factory method.
     *
     * @param KConfigInterface $config An optional KConfig object with configuration options
     *
     * @return KServiceInstantiatable
     */
    public static function setAttribute(KConfigInterface $config)
    {
        $description = $config->description;

        if (!$description) {
            throw new AnDomainAttributeException('description [AnDomainDescriptionAbstract] option is required');
        }

        if (empty($config->type)) {
            $config->append(array(
                'column' => AnInflector::underscore($config->name),
            ));

            $config->column = $description->getRepository()->getResources()->getColumn($config->column);

            if ($config->column) {
                $config->type = $config->column->type;
            } else {
                $config->type = 'string';
            }
        }

        //if a default options is set then
        //making the property requires allows using default options
        //for cases when a property is null unless stated otherwise
        if (isset($config->default)) {
            /*
            $config->append(array(
                    'required'=>true
            ));
            */
        }

        //prevents having a null value if
        //a default option is set or the require is set
        /*
        if ( $config->required || $config->default )
        {
            $config->append(array(
                    'required'	=> true,
                    'default' 	=> $config->type
            ));            
        }*/

        $config->append(array(
            'column' => AnInflector::underscore($config->name),
        ));

        if (is_string($config->column)) {
            $config->column = $description->getRepository()->getResources()->getColumn($config->column);
        }

        if (!$config->column) {
            throw new AnDomainPropertyException('Property '.$config->name.' is mapped to an invalid column for entity '.$description->getEntityIdentifier());
        }

        $property = AnDomainPropertyAbstract::getInstance('attribute.property', $config);

        if ($config->key) {
            $description->addIdentifyingProperty($property);
        }

        return $property;
    }

    /**
     * Relationship factory method.
     *
     * @param KConfigInterface $config An optional KConfig object with configuration options
     *
     * @return KServiceInstantiatable
     */
    public static function setRelationship(KConfigInterface $config)
    {
        $description = $config->description;

        if (!$description) {
            throw new AnDomainPropertyException('description [AnDomainDescriptionAbstract] option is required');
        }

        //determine the relationship type if not set
        //If name is sinuglar, then it's belongs 
        //If name is plurarl, then it's has
        if (empty($config['type'])) {
            if (AnInflector::isSingular($config['name'])) {
                $config['type'] = 'belongs_to';
            } else {
                $config['type'] = 'has';
            }
        }

        if ($config['type'] != 'has' && $config['type'] != 'belongs_to') {
            throw new AnDomainPropertyException("Incorrect relationship type specified: {$config->type}");
        }

        //create the relationship based on its type        
        if ($config['type'] == 'belongs_to') {
            $relationship = self::_belongsTo($config);
        } else {
            $relationship = self::_has($config);
        }

        return $relationship;
    }

    /**
     * Add many-to-many or one-to-many or one-to-one relationship to a mapper. The cardinality
     * of a relationship can be an integer or the string 'many'.
     *
     * @param KConfig $config Relationship options
     *
     * @return AnDomainRelationshipManytoone
     */
    protected static function _has(KConfig $config)
    {
        //since the mixer is a singleton use the mixer object directly
        $description = $config['description'];

        //set the default cardinality 
        //If name singular 1 else many 		
        $config->append(array(
            'cardinality' => AnInflector::isSingular($config['name']) ? 1 : 'many',
        ));

        $cardinality = (int) $config->cardinality;

        $config->parent = $description->getEntityIdentifier();

        //a one to one relationship
        if (is_numeric($config->cardinality) && (int) $cardinality == 1) {
            $relationship = AnDomainPropertyAbstract::getInstance('relationship.onetoone', $config);

            //add the belnogs to relation to the child description
            //if it doesn't exists. Since it's a one-to-one relatonship
            //the parent entity uniquly identifies the child entity
            $child_description = $relationship->getChildRepository()->getDescription();
            $property = $child_description->getProperty($relationship->getChildKey());

            if (!$property) {
                $property = $child_description->setRelationship($relationship->getChildKey(), array('type' => 'belongs_to', 'parent' => $relationship->getParent()));
            }

            if ($relationship->isRequired()) {
                $child_description->addIdentifyingProperty($property);
            }
        }
        //if a through is not set then it's just one-to-many relationship
        elseif (!$config->through) {
            $relationship = AnDomainPropertyAbstract::getInstance('relationship.onetomany', $config);

            //the child repository must have a belongs to relationship
            //if not then lets create one for it automatically
            if (!$relationship->getChildProperty()) {
                $child_key = $relationship->getChildKey();
                $belongs_to_options = array('parent' => $description->getEntityIdentifier(),'type' => 'belongs_to');

                if ($config->child_column) {
                    $belongs_to_options['child_column'] = $config->child_column;
                }

                if ($config->parent_key) {
                    $belongs_to_options['parent_key'] = $config->parent_key;
                }

                $property = $relationship->getChildRepository()->getDescription()->setRelationship($child_key, $belongs_to_options);
            }
        } else {
            $through_one_to_many = null;

            //if subscription is through a one-to-many relationship		
            if (strpos($config->through, '.') === false && $through_one_to_many = $description->getProperty($config->through)) {
                unset($config->through);

                $config->append(array(
                    'parent_delete' => $through_one_to_many->getDeleteRule(),
                    'as' => $through_one_to_many->getName(),
                    'through' => $through_one_to_many->getChild(),
                    'parent' => $through_one_to_many->getParent(),
                    'child_key' => $through_one_to_many->getChildKey(),
                    'parent_key' => $through_one_to_many->getParentKey(),
                ));
            }

            $relationship = AnDomainPropertyAbstract::getInstance('relationship.manytomany', $config);

            //lets create a child relationship for the parent
            //in the link entity if it doesn't exists			
            if (!$relationship->getChildProperty()) {
                $child_key = $relationship->getChildKey();

                $belongs_to_options = array('parent' => $description->getEntityIdentifier(),'type' => 'belongs_to');

                if ($config->child_column) {
                    $belongs_to_options['child_column'] = $config->child_column;
                }

                $property = $relationship->getChildRepository()->getDescription()->setRelationship($child_key, $belongs_to_options);
            }

            //lets create a child relationship for the target
            //in the link entity if it doesn't exists
            if (!$relationship->getTargetChildProperty()) {
                $child_key = $relationship->getTargetChildKey();

                $belongs_to_options = array('type' => 'belongs_to','parent' => $relationship->getTargetRepository()->getDescription()->getEntityIdentifier());

                if ($config->target_child_column) {
                    $belongs_to_options['target_child_column'] = $config->child_column;
                }

                $property = $relationship->getChildRepository()->getDescription()->setRelationship($child_key, $belongs_to_options);
            }

            //create has_many relationship for both the parent and the target
            //if there's no relationship set before
            $parent = $relationship->getJunctionAlias();
            $target = $parent;

            if (empty($through_one_to_many)) {
                $through_one_to_many = $description->setRelationship($target, array(
                        'type' => 'has',
                        'cardinality' => $config->cardinality,
                        'child_key' => $relationship->getChildKey(),
                        'parent_key' => $relationship->getParentKey(),
                        'child' => $relationship->getChild(),
                        'parent_delete' => $relationship->getDeleteRule(),
                ));
            } elseif ($through_one_to_many->getName() != $target) {
                $description->setAlias($through_one_to_many->getName(), $target);
            }

            $through_one_to_many_target = $relationship->getTargetRepository()->getDescription()->setRelationship(
                             $parent, array(
                                'cardinality' => $config->cardinality,
                                'type' => 'has',
                                'child_key' => $relationship->getTargetChildKey(),
                                'parent_key' => $relationship->getTargetParentKey(),
                                'child' => $relationship->getChild(),
                                'parent_delete' => $relationship->getDeleteRule(),
                            ));

            /*
             * Duplicate Delete for two object that have has many to has many 
             * relationship with each other 
             */
// 			print $through_one_to_many_target->getParentRepository()->getIdentifier()->name.' has many '.$through_one_to_many_target->getName().'<br/>'.
// 			$through_one_to_many->getParentRepository()->getIdentifier()->name.' has many '.$through_one_to_many->getName().'<br/><hr/>';
        }

        return $relationship;
    }

    /**
     * Creates a belongs to relationship (many to one).
     * 	 	 
     * @param KConfig $config Relationship options
     * 
     * @return AnDomainRelationshipManytoone
     */
    protected static function _belongsTo(KConfig $config)
    {
        $description = $config['description'];

        $config['child'] = $description->getEntityIdentifier();

        $config->append(array(
            'type_column' => AnInflector::underscore($config->name).'_type',
            'child_column' => AnInflector::underscore($config->name).'_id',
        ));

        if (is_string($config->type_column)) {
            $config->type_column = $description->getRepository()->getResources()->getColumn($config->type_column);
        }

        if (is_string($config->child_column)) {
            $config->child_column = $description->getRepository()->getResources()->getColumn($config->child_column);
        }

        if (!$config->child_column) {
            throw new AnDomainPropertyException('The '.$config->name.' belongs to relationship is missing a child column');
        }

        //if the relationship is not polymorphic the we need to set 
        //a parent if none is set
        if (!$config->polymorphic && !$config['parent']) {
            $parent = clone $description->getEntityIdentifier();
            $parent->name = $config['name'];
            $config->append(array(
                'parent' => $parent,
            ));
        }

        if (is_string($config->parent) && strpos($config->parent, '.') === false) {
            $parent = clone $description->getEntityIdentifier();
            $parent->name = $config->parent;
            $config->parent = $parent;
        }

        $relationship = AnDomainPropertyAbstract::getInstance('relationship.manytoone', $config);

        if ($config->inverse) {
            if (is_bool($config->inverse)) {
                $config['inverse'] = array();
            }

            $relationship->setInverse($config->inverse);
        }

        return $relationship;
    }
}
