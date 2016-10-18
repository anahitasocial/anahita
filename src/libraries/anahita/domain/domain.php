<?php

/**
 * Anahita Domain.
 *
 * Domain offers classes for domain driven programming. Domain Package implements
 * Unit of Work, Data Mapper, Domain Query patterns
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnDomain
{
    /**
     * Property Access Constants.
     */
    const ACCESS_PRIVATE = 0;
    const ACCESS_PROTECTED = 1;
    const ACCESS_PUBLIC = 2;

    /**
     * Entity States.
     */
    const STATE_CLEAN = 2;
    //pre-committ states
    const STATE_NEW = 4;
    const STATE_MODIFIED = 8;
    const STATE_DELETED = 16;

    //internal states
    const STATE_COMMITABLE = 28;
    //internal states
    const STATE_INSERTED = 32;
    const STATE_UPDATED = 64;
    const STATE_DESTROYED = 128;
    const STATE_COMMITTED = 224;

    /**
     * Delete Rules.
     */
    const DELETE_CASCADE = 'cascade';
    const DELETE_DESTROY = 'destroy';
    const DELETE_DENY = 'deny';
    const DELETE_NULLIFY = 'nullify';
    const DELETE_IGNORE = 'ignore';

    /**
     * Fetch Mode.
     */
    const FETCH_ROW = 1;
    const FETCH_VALUE = 2;
    const FETCH_ENTITY = 4;
    const FETCH_VALUE_LIST = 8;
    const FETCH_ROW_LIST = 16;
    const FETCH_ENTITY_SET = 32;
    const FETCH_ENTITY_LIST = 64;

    /**
     * Fetch Check.
     */
    const FETCH_ITEM = 7;
    const FETCH_LIST = 112;

    /**
     * Entity operations.
     */
    const OPERATION_FETCH = 1;
    const OPERATION_INSERT = 2;
    const OPERATION_UPDATE = 4;
    const OPERATION_DELETE = 8;
    const OPERATION_DESTROY = 16;
    const OPERATION_COMMIT = 30;

    /**
     * Require Flags. A NOT_NULL value can be empty such as 0 or ''
     * A NOT_EMPTy value can not be '', 0 or null. The requireds are by
     * default VALUE_NO_NULL unless explicitly set in the entity description.
     */
    const VALUE_NOT_NULL = true;
    const VALUE_NOT_EMPTY = 1;

    /**
     * Entity Identifers must have application in their path. This method set the
     * application of an identifier if the path is missing.
     *
     * @param string $identifier Entity Identifier
     *
     * @return KServiceIdentifier
     */
    public static function getEntityIdentifier($identifier)
    {
        $identifier = KService::getIdentifier($identifier);

        if (!$identifier->basepath) {
            $adapters = KService::get('koowa:loader')->getAdapters();
            $basepath = pick($adapters[$identifier->type]->getBasePath(), ANPATH_BASE);
            $applications = array_flip(KServiceIdentifier::getApplications());

            if (isset($applications[$basepath])) {
                $identifier->application = $applications[$basepath];
                $identifier->basepath = $basepath;
            }
        }

        return $identifier;
    }

    /**
     * Helper mehtod to return a repository for an entity.
     *
     * @param string $identifier Entity Identifier
     * @param array  $config     Configuration
     *
     * @return AnDomainRepositoryAbstract
     */
    public static function getRepository($identifier, $config = array())
    {
        if (strpos($identifier, 'repos:') === 0) {
            $repository = KService::get($identifier);
        } else {
            $strIdentifier = (string) $identifier;

            if (!KService::has($identifier)) {
                $identifier = self::getEntityIdentifier($identifier);
            }

            if (!KService::has($identifier)) {
                KService::set($strIdentifier, KService::get($identifier, $config));
            }

            $repository = KService::get($identifier)->getRepository();
        }

        return $repository;
    }
}
