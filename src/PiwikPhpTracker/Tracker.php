<?php
namespace PiwikPhpTracker;

use Exception;

class Tracker extends Parameters
{

    /**
     *
     * @var string
     */
    protected $apiUrl;

    /**
     * Request timeout in seconds
     *
     * @var integer
     */
    protected $requestTimeout = 10;

    /**
     *
     * @param integer $idSite            
     * @param string $apiUrl            
     */
    public function __construct($apiUrl, $idSite)
    {
        $this->setApiUrl($apiUrl);
        $this->setIdSite($idSite);
        
        $this->setParameter('rand', md5(time()));
        $this->initServerParameters();
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
     * Sets the maximum number of seconds that the tracker will spend waiting for a response
     * from Piwik.
     *
     * @param integer $seconds            
     * @throws Exception
     */
    public function setRequestTimeout($seconds)
    {
        if (! is_int($seconds) || $seconds < 0) {
            throw new Exception('Invalid value supplied for request timeout: ' . $seconds);
        }
        
        $this->requestTimeout = (int) $seconds;
    }

    /**
     * Returns the maximum number of seconds the tracker will spend waiting for a response
     * from Piwik.
     *
     * @return integer
     */
    public function getRequestTimeout()
    {
        return $this->requestTimeout;
    }

    /**
     * Builds URL to track a page view.
     *
     * @see doTrackPageView()
     * @param string $documentTitle
     *            Page view name as it will appear in Piwik reports
     * @return string URL to piwik.php with all parameters set to track the pageview
     */
    public function getUrlTrackPageView($documentTitle = '')
    {
        $url = $this->getRequestUrl();
        if (strlen($documentTitle) > 0) {
            $url .= '&action_name=' . urlencode($documentTitle);
        }
        
        return $url;
    }

    /**
     *
     * @param string $documentTitle            
     * @return string
     */
    public function doTrackPageView($documentTitle)
    {
        $url = $this->getUrlTrackPageView($documentTitle);
        return $this->sendRequest($url);
    }

    /**
     * Returns the base URL for the piwik server.
     */
    protected function getBaseUrl()
    {
        $apiUrl = $this->getApiUrl();
        
        if (strpos($apiUrl, '/piwik.php') === false && strpos($apiUrl, '/proxy-piwik.php') === false) {
            $apiUrl .= '/piwik.php';
        }
        return $apiUrl;
    }

    protected function getRequestUrl()
    {
        return $this->getBaseUrl() . '?' . $this->getQueryParameters();
    }

    /**
     *
     * @param string $url            
     * @param string $method            
     * @param string $data            
     * @param boolean $force            
     * @return string
     */
    protected function sendRequest($url, $method = 'GET', $data = null, $force = false)
    {
        $content = '';
        
        if (function_exists('curl_init')) {
            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_USERAGENT => $this->getUserAgent(),
                CURLOPT_HEADER => true,
                CURLOPT_TIMEOUT => $this->getRequestTimeout(),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    'Accept-Language: ' . $this->getLanguage()
                )
            );
            
            if ($method === 'POST') {
                $options[CURLOPT_POST] = true;
            }
            
            // only supports JSON data
            if (! empty($data)) {
                $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
                $options[CURLOPT_HTTPHEADER][] = 'Expect:';
                $options[CURLOPT_POSTFIELDS] = $data;
            }
            
            $ch = curl_init();
            curl_setopt_array($ch, $options);
            ob_start();
            $response = @curl_exec($ch);
            ob_end_clean();
            
            if (! empty($response)) {
                list (, $content) = explode("\r\n\r\n", $response, $limitCount = 2);
            }
        } else {
            if (function_exists('stream_context_create')) {
                $stream_options = array(
                    'http' => array(
                        'method' => $method,
                        'user_agent' => $this->getUserAgent(),
                        'header' => "Accept-Language: " . $this->getLanguage() . "\r\n",
                        'timeout' => $this->getRequestTimeout()
                    )
                );
                
                // only supports JSON data
                if (! empty($data)) {
                    $stream_options['http']['header'] .= "Content-Type: application/json \r\n";
                    $stream_options['http']['content'] = $data;
                }
                $ctx = stream_context_create($stream_options);
                $content = file_get_contents($url, 0, $ctx);
            }
        }
        
        return $content;
    }
}
