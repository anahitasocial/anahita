<?

class ComPeopleDomainRepositorySession extends AnDomainRepositoryDefault
{
    public function purge($maxLifetime = 7200)
    {
        $past = time() - $maxLifetime;

        $this->getQuery()->delete()->where('time < '. (int) $past);

        return $this;
    }
}
