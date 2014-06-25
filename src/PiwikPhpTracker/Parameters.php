<?php
namespace PiwikPhpTracker;

class Parameters
{

    const FIRST_PARTY_COOKIES_PREFIX = '_pk_';

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
        
        // optional USER information
        'urlref' => null,
        '_cvar' => null,
        '_idvc' => null,
        '_viewts' => null,
        '_idts' => null,
        '_rcn' => null,
        '_rck' => null,
        'res' => null,
        'h' => null,
        'm' => null,
        's' => null,
        'ua' => null,
        'lang' => null,
        
        // optional ACTION information
        
        // special parameters (SUPER USER needed)
        'token_auth' => null,
        'cip' => null,
        'cdt' => null,
        'cid' => null,
        'new_visit' => null,
        'country' => null,
        'region' => null,
        'city' => null,
        'lat' => null,
        'long' => null
    );

    /**
     * Set the default values by the server vars and cookie
     */
    protected function initServerParameters()
    {
        $this->setUrl($this->getCurrentUrl());
        
        if (! empty($_SERVER['HTTP_REFERER'])) {
            $this->setReferrer($_SERVER['HTTP_REFERER']);
        }
        
        if (! empty($_SERVER['REMOTE_ADDR'])) {
            $this->setIp($_SERVER['REMOTE_ADDR']);
        }
        
        if (! empty($_SERVER['HTTP_USER_AGENT'])) {
            $this->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        }
        
        if (! empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $this->setLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        }
    }

    /**
     * If current URL is "http://example.org/dir1/dir2/index.php?param1=value1&param2=value2"
     * will return "/dir1/dir2/index.php"
     *
     * @return string
     * @ignore
     *
     */
    protected function getCurrentScriptName()
    {
        $url = '';
        if (! empty($_SERVER['PATH_INFO'])) {
            $url = $_SERVER['PATH_INFO'];
        } else 
            if (! empty($_SERVER['REQUEST_URI'])) {
                if (($pos = strpos($_SERVER['REQUEST_URI'], '?')) !== false) {
                    $url = substr($_SERVER['REQUEST_URI'], 0, $pos);
                } else {
                    $url = $_SERVER['REQUEST_URI'];
                }
            }
        if (empty($url)) {
            $url = $_SERVER['SCRIPT_NAME'];
        }
        
        if ($url[0] !== '/') {
            $url = '/' . $url;
        }
        return $url;
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
     * will return "example.org"
     *
     * @return string
     */
    protected function getCurrentHost()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }
        
        return 'unknown';
    }

    /**
     * If current URL is "http://example.org/dir1/dir2/index.php?param1=value1&param2=value2"
     * will return "?param1=value1&param2=value2"
     *
     * @return string
     */
    protected function getCurrentQueryString()
    {
        if (isset($_SERVER['QUERY_STRING']) && ! empty($_SERVER['QUERY_STRING'])) {
            return '?' . $_SERVER['QUERY_STRING'];
        }
        
        return '';
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
     * @param string $name            
     * @param mixed $value            
     */
    protected function setParameter($name, $value)
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
        $this->setParameter('url', (string) $url);
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
        $this->setParameter('urlref', (string) $url);
    }

    public function getReferrer()
    {
        return $this->getParameter('urlref');
    }

    public function setIp($ip)
    {
        $this->setParameter('cip', (string) $ip);
    }

    public function getIp()
    {
        return $this->getParameter('cip');
    }

    public function setUserAgent($userAgent)
    {
        $this->setParameter('ua', (string) $userAgent);
    }

    public function getUserAgent()
    {
        return $this->getParameter('ua');
    }

    public function setLanguage($language)
    {
        $this->setParameter('lang', (string) $language);
    }

    public function getLanguage()
    {
        return $this->getParameter('lang');
    }

    protected function getQueryParameters()
    {
        $parameters = array();
        foreach ($this->getParameters() as $key => $value) {
            if ($value !== null) {
                $parameters[] = $key . '=' . urlencode($value);
            }
        }
        
        return implode('&', $parameters);
    }
}
