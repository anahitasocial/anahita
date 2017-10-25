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
 * @link       http://www.GetAnahita.com
 */
class LibBaseControllerBehaviorCommittable extends AnControllerBehaviorAbstract
{
    /**
     * Failed entities in the last commit.
     *
     * @var KObjectSet
     */
    protected $_failed_commits;

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority' => KCommand::PRIORITY_HIGHEST,
        ));

        parent::_initialize($config);
    }

    /**
     * Executes a commit after each action. This prevents having too many
     * manuall commit.
     *
     * @param string          $name    The command name
     * @param KCommandContext $context The command context
     *
     * @return bool Can return both true or false.
     */
    public function execute($name, KCommandContext $context)
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

            $type = $result === false ? 'error' : 'success';
            $message = $this->_makeStatusMessage($context->action, $type);

            if ($message) {
                $this->setMessage($message, $type);
            }

            if ($result === false) {
                if ($this->isIdentifiable() && $this->getItem()) {
                    if ($this->getItem()->getErrors()->count()) {
                        throw new AnErrorException($this->getItem()->getErrors(), KHttpResponse::BAD_REQUEST);
                        return;
                    }
                } else {
                    $errors = AnHelperArray::getValues($this->getCommitErrors());
                    throw new AnErrorException($errors, KHttpResponse::BAD_REQUEST);
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
     * Render a message for an action.
     *
     * @param string $action The action name whose message is being built
     * @param string $type   The type of the message. The type can be success, error or info
     *
     * @return string Return the built message
     */
    protected function _makeStatusMessage($action, $type = 'success')
    {
        $messages = array();
        $messages[] = strtoupper('COM-'.$this->_mixer->getIdentifier()->package.'-PROMPT-'.$this->_mixer->getIdentifier()->name.'-'.$action.'-'.$type);
        $messages[] = strtoupper('LIB-AN-MESSAGE-'.$this->_mixer->getIdentifier()->name.'-'.$action.'-'.$type);
        $messages[] = strtoupper('LIB-AN-MESSAGE-'.$action.'-'.$type);
        $messages[] = 'LIB-AN-PROMPT-COMMIT-'.strtoupper($type);
        $message = translate($messages, false);

        return $message;
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
     * @return KObjectSet
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
        return KMixinAbstract::getHandle();
    }
}
