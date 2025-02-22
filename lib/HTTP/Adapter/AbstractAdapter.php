<?php
/**
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

require_once(ROOT.DS.APP_DIR.DS.'lib/API/APIException.php');
require_once(ROOT.DS.APP_DIR.DS.'lib/API/WoW/Exceptions/NotFoundException.php');
require_once(ROOT.DS.APP_DIR.DS.'lib/Cache/Cache.php');
require_once(ROOT.DS.APP_DIR.DS.'lib/HTTP/Response/AbstractResponse.php');

abstract class AbstractAdapter
{
    /**
     * Http Header
     * 
     * @access protected
     * @var array
     */
    protected $headers = array();

    /**
     * Cache
     * 
     * @var Cache
     */
    protected $_cache;

    public function setCache(Cache $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Execute the Api Call
     *
     * @access public
     * @param AbstractCall $call
     * @return ApiResponse
     * @throws ApiException
     */
    public function request($method, $url, array $params = array(), array $options = array())
    {
        $this->_loadOptions($options);

        // assemble the query string
        $queryParams = $this->_getQueryParams($params);

        switch ( $method ) {
            case 'GET':
                $response = $this->_getRequest($method, $url, $queryParams);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Method "%s" is not supported',$method));
                break;
        }
        
        $this->_handleResponse($response);
        
        return $response;
    }

    protected function _handleResponse(AbstractResponse $response)
    {
        $httpCode = $response->getResponseCode();
        $httpMessage = $response->getResponseMessage();

        switch ( $httpCode ) {
            case 404:
                throw new NotFoundException($httpMessage, $httpCode);
                break;
            case 500:
                throw new ApiException($httpMessage, $httpCode);
                break;
			case 503:
				throw new ApiException($httpMessage, $httpCode);
				break;
			case 0:
				throw new HttpException($httpMessage, $httpCode);
				break;
        }
    }
    
    protected function _loadOptions($options)
    {
        foreach ( $options as $name => $value )
        {
            $methodName = 'set'.ucfirst($name);
            if ( method_exists($this, $methodName) ) {
                $this->$methodName($value);
            }
        }
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    protected function getHeaders()
    {
        $header = '';
        foreach ( $this->headers as $name => $value ) {
            $header .= $name.":".$value."\r\n";
        }
        return $header;
    }

    /**
     * assemble the Query string.
     *
     * @access protected
     * @param AbstractCall $call
     * @return string
     */
    protected function _getQueryParams($params)
    {
        foreach ( $params as $i => $param ) {
            if ( is_array($param) ) {
                $param = $this->_getQueryParams($param);
            }
        }
        return http_build_query($params);
    }

    /**
     * Execute the GET Api Call
     *
     * @access private
     * @param AbstractCall $call
     * @param string $baseUrl
     * @param string $queryParams
     * @return ApiResponse
     */
    private function _getRequest($method, $url, $queryParams)
    {
        // retrieve the cacheId for this url.
        $cacheId = $this->_getCacheId($url.'?'.$queryParams);

        // check if this request isn't already cached
        if ( $this->_isCached($cacheId) ) {
            // retrieved the cached response
            $response = $this->_fromCache($cacheId);
        } else {
            $response = $this->_doRequest($method,$url, $queryParams);

            // cache the response
            $this->_cache($cacheId,$response);
        }

        return $response;
    }

    abstract protected function _doRequest($method, $url, $params);

    /**
     * Check if a request already exists in the cache
     *
     * @access private
     * @param string $cacheId
     * @return boolean
     */
    private function _isCached($cacheId)
    {
        // check if a cache interface is set
        if ( isset($this->_cache) ) {
            return $this->_cache->contains($cacheId);
        }
        return false;
    }

    /**
     * Fetch a request from cache
     *
     * @access private
     * @param string $cacheId
     * @return ApiResponse
     */
    private function _fromCache($cacheId)
    {
        return $this->_cache->fetch($cacheId);
    }

    /**
     * Save a response in the cache
     *
     * @access private
     * @param string $cacheId
     * @param ApiResponse $response
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    private function _cache($cacheId, $response)
    {
        // check if a cache interface is set
        if ( isset($this->_cache) ) {
            return $this->_cache->save($cacheId, $response, $response->getTTL());
        }
        return false;
    }

    /**
     * Get the cache identifier for the $url
     *
     * @access private
     * @param string $url
     * @return string
     */
    private function _getCacheId($url)
    {
        return md5($url);
    }
}