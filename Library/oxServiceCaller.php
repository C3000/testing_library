<?php
/**
 * This file is part of OXID eSales Testing Library.
 *
 * OXID eSales Testing Library is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales Testing Library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales Testing Library. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

require_once 'oxTestCurl.php';

/**
 * Class for calling services. Services must already exist in shop.
 */
class oxServiceCaller
{

    /** @var array */
    private $_aParameters = array();

    /**
     * Sets given parameters.
     *
     * @param string $sKey Parameter name.
     * @param string $aVal Parameter value.
     */
    public function setParameter($sKey, $aVal)
    {
        $this->_aParameters[$sKey] = $aVal;
    }

    /**
     * Returns array of parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->_aParameters;
    }

    /**
     * Call shop service to execute code in shop.
     *
     * @param string $sServiceName
     * @param string $sShopId
     *
     * @example call to update information to database.
     *
     * @throws Exception
     *
     * @return string $sResult
     */
    public function callService($sServiceName, $sShopId = null)
    {
        if ($sShopId && oxSHOPID != 'oxbaseshop') {
            $this->setParameter('shp', $sShopId);
        } elseif (isSUBSHOP) {
            $this->setParameter('shp', oxSHOPID);
        }

        $oCurl = new oxTestCurl();

        $sShopUrl = shopURL . '/Services/service.php';
        $this->setParameter('service', $sServiceName);

        $oCurl->setUrl($sShopUrl);
        $oCurl->setParameters($this->getParameters());

        $sResponse = $oCurl->execute();

        if ($oCurl->getStatusCode() >= 300) {
            $sResponse = $oCurl->execute();
        }

        $this->_aParameters = array();

        return $this->_unserializeString($sResponse);
    }

    /**
     * Unserializes given string. Throws exception if incorrect string is passed
     *
     * @param string $sString
     *
     * @throws Exception
     *
     * @return mixed
     */
    private function _unserializeString($sString)
    {
        $mResult = unserialize($sString);
        if ($sString !== 'b:0;' && $mResult === false) {
            throw new Exception(substr($sString, 0, 500));
        }

        return $mResult;
    }
}
