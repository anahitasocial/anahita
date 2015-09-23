<?php

/**
 * File View.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseViewFile extends LibBaseViewFile
{
    /**
     * Return the views output.
     *
     *  @return string 	The output of the view
     */
    public function display()
    {
        if ($this->entity && $this->entity->isFileable()) {
            $this->output = $this->entity->getFileContent();
            $this->filename = $this->entity->getFileName();
        }

        return parent::display();
    }
}
