<?php

/**
 * Set Entity.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComPhotosDomainEntitySet extends ComMediumDomainEntityMedium
{
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
            'attributes' => array(
                'name' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'length' => array(
                        'max' => 100,
                    ),
                ),
            ),
            'behaviors' => array(
                'hittable',
            ),
            'relationships' => array(
                'photos' => array('through' => 'edge'),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Obtains the image file source.
     *
     * @return string path to image source
     *
     * @param $size photo size. One of the constan sizes in the ComPhotosDomainEntityPhoto class
     */
    public function getCoverSource($size = ComPhotosDomainEntityPhoto::SIZE_SQUARE)
    {
        $cover = $this->photos->order('photoSets.ordering')->fetch();
        $filename = $cover->filename;

        //get file extension
        $extension = explode('.', $filename);
        $extension = array_pop($extension);

        //remove file extension
        $name = preg_replace('#\.[^.]*$#', '', $filename);
        $filename = $name.'_'.$size.'.'.$extension;

        return $this->owner->getPathURL('com_photos/'.$filename);
    }

    /**
     * Adds a photo to a set.
     *
     * @return true on success
     *
     * @param $photo a ComPhotosDomainEntityPhoto object
     */
    public function addPhoto($photo)
    {
        $photos = AnHelperArray::getIterator($photo);

        foreach ($photos as $photo) {
            if (!$this->photos->find($photo)) {
                $this->photos->insert($photo, array(
                     'author' => $photo->author,
                ));
            }
        }
    }

    /**
     * Removes a photo or list of photos from the set.
     *
     * @param $photo a ComPhotosDomainEntityPhoto object
     */
    public function removePhoto($photo)
    {
        $photos = AnHelperArray::getIterator($photo);

        foreach ($photos as $photo) {
            if ($edge = $this->photos->find($photo)) {
                $edge->delete();
            }
        }
    }

    /**
     * Orders the photos in this set.
     *
     * @param array $photo_ids
     */
    public function reorder($photo_ids)
    {
        if (count($photo_ids) == 1) {
            if ($edge = $this->getService('repos:photos.edge')
                              ->fetch(array(
                                        'set' => $this,
                                        'photo.id' => $photo_ids[0],
                                      ))
            ) {
                $edge->ordering = $this->photos->getTotal();
            }

            return;
        }

        foreach ($photo_ids as $index => $photo_id) {
            if ($edge = $this->getService('repos:photos.edge')
                             ->fetch(array(
                                      'set' => $this,
                                      'photo.id' => $photo_id, ))
            ) {
                $edge->ordering = $index + 1;
            }
        }
    }

    /**
     * Gets number of photos in this set.
     *
     * @return int value
     */
    public function getPhotoCount()
    {
        return $this->getValue('photo_count', 0);
    }
}
