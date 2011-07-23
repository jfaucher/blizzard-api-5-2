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

namespace Khepri\BattleNet\Api\Wow\Call\Realm;

use Khepri\BattleNet\Api\AbstractCall;

/**
 * Call for the WoW Realm Status Api
 *
 * @author 		Jelte Steijaert <jelte AT 4tueel DOT be>
 * @version		0.1.0
 */
class StatusCall
    extends AbstractCall
{
    /**
     * Path for the Realm Status Call
     * 
     * @access protected
     * @var string
     */
    protected $_path = 'realm/status';
                
    /**
     * allowed query parameters
     * 
     * @access protected
     * @var array
     */
    protected $_whitelist = array('realm');

    /**
     * Constructor
     * 
     * @access public
     * @param string $realm
     * @return void
     * @constructor
     */
    public function __construct($realm = null)
    {
        $this->setRealm($realm);
    }
    
    /**
     * Set the realm name
     * 
     * @access public
     * @param string $realm
     * @return void
     */
    public function setRealm($realm)
    {
        $this->setQueryParam('realm', $realm);
    }
    
}