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

 require_once(ROOT.DS.APP_DIR.DS.'lib/Cache/AbstractCache.php');

/**
* Zend Data Cache cache driver.
*
* @author Jelte Steijaert <jelte AT 4tueel DOT be>
*/
class ZendDataCache extends AbstractCache
{
    public function __construct()
    {
        $this->setNamespace('battlenet::'); // zend data cache format for namespaces ends in ::
    }

    /**
* {@inheritdoc}
*/
    protected function _doFetch($id)
    {
        return zend_shm_cache_fetch($id);
    }

    /**
* {@inheritdoc}
*/
    protected function _doContains($id)
    {
        return (zend_shm_cache_fetch($id) !== FALSE);
    }

    /**
* {@inheritdoc}
*/
    protected function _doSave($id, $data, $lifeTime = 0)
    {
        return zend_shm_cache_store($id, $data, $lifeTime);
    }

    /**
* {@inheritdoc}
*/
    protected function _doDelete($id)
    {
        return zend_shm_cache_delete($id);
    }
}