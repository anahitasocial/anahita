<?php

/**
 * Store Interface.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
interface AnDomainStoreInterface
{
    /**
     * Select a result from a store. There are three diffenret fetch modes to return result.
     *
     * @param AnDomainQuery $query Query object
     * @param int           $mode  Fetch Mode
     *
     * @return mixed
     */
    public function fetch($query, $mode);

    /**
     * Inserts an entity into the persistant store. It will return the insertId.
     *
     * @param AnDomainRepositoryAbstract $repository
     * @param array                      $data
     *
     * @return int
     */
    public function insert($repository, $data);

    /**
     * Updates entities records in a reposistory using the $keys with the passed in data.
     *
     * @param AnDomainRepositoryAbstract $repository
     * @param array                      $keys
     * @param array                      $data
     */
    public function update($repository, $keys, $data);

    /**
     * Delete enitites identified by $keys in the repository.
     *
     * @param AnDomainRepositoryAbstract $repositoy
     * @param array                      $keys
     *
     * @return bool
     */
    public function delete($repository, $keys);

    /**
     * Quotes a value.
     *
     * @param string $value A value
     *
     * @return
     */
    public function quoteValue($value);

    /**
     * Quotes a name.
     *
     * @param string $name A name
     *
     * @return
     */
    public function quoteName($name);

    /**
     * Return an array of columns for a resource.
     *
     * @param $resource Resrouce name
     *
     * @return array
     */
    public function getColumns($resource);

    /**
     * Executes a query.
     *
     * @param string $query
     *
     * @return bool
     */
    public function execute($query);
}
