<?php

/**
 * Commitable behavior provides API to interace with domain context and storing
 * the last save result.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class LibBaseControllerBehaviorCommittable extends AnControllerBehaviorAbstract
{
    /**
     * Failed entities in the last commit.
     *
     * @var AnObjectSet
     */
    protected $_failed_commits;

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'priority' => AnCommand::PRIORITY_HIGHEST,
        ));

        parent::_initialize($config);
    }

    /**
     * Executes a commit after each action. This prevents having too many
     * manuall commit.
     *
     * @param string          $name    The command name
     * @param AnCommandContext $context The command context
     *
     * @return bool Can return both true or false.
     */
    public function execute($name, AnCommandContext $context)
    {
        $parts = explode('.', $name);
        $result = $context->result;

        //after an action save
        if ($parts[0] === 'after' && $parts[1] != 'cancel') {

            //skip if there are not any commitable
            if (count($this->getRepository()->getSpace()->getCommitables()) == 0) {
                return;
            }

            $result = $this->commit();

            if ($result === false) {
                if ($this->isIdentifiable() && $this->getItem()) {
                    if ($this->getItem()->getErrors()->count()) {
                        throw new AnErrorException($this->getItem()->getErrors(), AnHttpResponse::BAD_REQUEST);
                        return;
                    }
                } else {
                    $errors = AnHelperArray::getValues($this->getCommitErrors());
                    throw new AnErrorException($errors, AnHttpResponse::BAD_REQUEST);
                    return;
                }
            }
        }
    }

    /**
     * Commits all the entities in the space.
     *
     * @return bool
     */
    public function commit()
    {
        return $this->getRepository()
        ->getSpace()
        ->commitEntities($this->_failed_commits);
    }

    /**
     * Return an array of commit errors.
     *
     * @return array
     */
    public function getCommitErrors()
    {
        $errors = array();

        if ($this->_failed_commits) {
            foreach ($this->_failed_commits as $entity) {
                $errors[(string) $entity->getIdentifier()] = array_values($entity->getErrors()->toArray());
            }
        }

        return $errors;
    }

    /**
     * Return a set of entities that failed the commits.
     *
     * @return AnObjectSet
     */
    public function getFailedCommits()
    {
        return $this->_failed_commits;
    }

    /**
     * Return the object handle.
     *
     * @return string
     */
    public function getHandle()
    {
        return AnMixinAbstract::getHandle();
    }
}
