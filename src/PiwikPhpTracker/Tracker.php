<?php
namespace PiwikPhpTracker;

/**
 *
 * @see API docs http://developer.piwik.org/api-reference/tracking-api
 */
class Tracker
{

    /**
     *
     * @var string
     */
    protected $apiUrl;

    /**
     * See parameter list here http://developer.piwik.org/api-reference/tracking-api
     *
     * @var array
     */
    protected $parameters = array(
        // required parameters
        'idsite' => null,
        'rec' => 1,
        'url' => null,
        
        // recommended
        'action_name' => null,
        '_id' => null,
        'rand' => null,
        'apiv' => 1,
        
        // optional
        'urlref' => null,
        
        'userAgent' => null,
        'localHour' => null
    );

    /**
     *
     * @param integer $idSite            
     * @param string $apiUrl            
     */
    public function __construct($apiUrl, $idSite)
    {
        $this->setApiUrl($apiUrl);
        $this->setIdSite($idSite);
        
        $this->initServerParameters();
    }

    /**
     * Set the default values by the server vars
     */
    protected function initServerParameters()
    {
        $this->setUrl($this->getCurrentUrl());
        
        if (! empty($_SERVER['HTTP_REFERER'])) {
            $this->setReferrer($_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * If the current URL is 'http://example.org/dir1/dir2/index.php?param1=value1&param2=value2"
     * will return 'http'
     *
     * @return string
     */
    protected function getCurrentScheme()
    {
        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] === true)) {
            return 'https';
        }
        
        return 'http';
    }

    /**
     * If current URL is "http://example.org/dir1/dir2/index.php?param1=value1&param2=value2"
     * will return "?param1=value1&param2=value2"
     *
     * @return string
     */
    protected function getCurrentQueryString()
    {
        $url = '';
        if (isset($_SERVER['QUERY_STRING']) && ! empty($_SERVER['QUERY_STRING'])) {
            $url .= '?' . $_SERVER['QUERY_STRING'];
        }
        return $url;
    }

    /**
     * Returns the current full URL (scheme, host, path and query string.
     *
     * @return string
     */
    protected function getCurrentUrl()
    {
        return $this->getCurrentScheme() . '://' . $this->getCurrentHost() . $this->getCurrentScriptName() . $this->getCurrentQueryString();
    }

    /**
     *
     * @param string $apiUrl            
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = (string) $apiUrl;
    }

    /**
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     *
     * @param string $name            
     * @param mixed $value            
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     *
     * @param string $name            
     * @return mixed
     */
    public function getParameter($name)
    {
        return $this->parameters[$name];
    }

    /**
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     *
     * @param integer $idSite            
     */
    public function setIdSite($idSite)
    {
        $this->setParameter('idsite', (int) $idSite);
    }

    /**
     *
     * @return integer
     */
    public function getIdSite()
    {
        return $this->getParameter('idsite');
    }

    /**
     *
     * @param string $url            
     */
    public function setUrl($url)
    {
        $this->setParameter('url', (string) $idSite);
    }

    /**
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->getParameter('url');
    }

    public function setReferrer($url)
    {
        $this->setParameter('urlref', $url);
    }

    public function getReferrer()
    {
        return $this->getParameter('urlref');
    }
}
